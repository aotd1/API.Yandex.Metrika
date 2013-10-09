<?php namespace Yandex;

class Metrika extends ApiBase
{
    protected static $service = 'https://api-metrika.yandex.ru';

    public static $dictType = array('simple', 'partner');
    public static $dictPermission = array('own', 'view', 'edit');
    public static $dictField = array('mirrors', 'goals', 'filters', 'operations', 'grants');
    public static $dictGroup = array('day', 'week', 'month');

    /**
     * Список доступных счетчиков
     * GET /counters
     * @param string $type - simple, partner
     * @param string $permission - own, view, edit
     * @param string $ulogin
     * @param array $field - array of mirrors, goals, filters, operations, grants
     * @return array
     * @throws ApiException
     * @link http://api.yandex.ru/metrika/doc/ref/reference/get-counter-list.xml
     */
    public function getCounters($type = null, $permission = null, $ulogin = null, array $field = array())
    {
        $options['type'] = self::checkDictionary($type, 'type');
        $options['permission'] = self::checkDictionary($permission, 'permission');

        if (!empty($ulogin)) {
            $options['ulogin'] = $ulogin;
        }

        if (!empty($field)) {
            foreach ($field as $item) {
                self::checkDictionary($item, 'field');
            }
            $options['field'] = implode(',', $field);
        }

        $result = $this->request('GET', '/counters.json', $options);
        if (isset($result['counters'])) {
            return $result['counters'];
        } else {
            return null;
        }
    }

    /**
     * Возвращает информацию об указанном счетчике.
     * GET /counter/{id}
     * @param int $id
     * @param array $field additional fields
     * @return array|null
     * @throws ApiException
     * @link http://api.yandex.ru/metrika/doc/ref/reference/get-counter.xml
     */
    public function getCounter($id, array $field = array())
    {
        $options = array();
        if (!empty($field)) {
            foreach ($field as $item)
                self::checkDictionary($item, 'field');
            $options['field'] = implode(',', $field);
        }

        $result = $this->request('GET', "/counter/$id.json", $options);
        if (isset($result['counter'])) {
            return $result['counter'];
        } else {
            return null;
        }
    }

    /**
     * @param string $name
     * @param array $field additional fields
     * @return array|null
     * @throws ApiException
     */
    public function getCounterByName($name, array $field = array())
    {
        //@TODO cache getCounters command
        $counters = $this->getCounters(null, null, null, $field);
        foreach ($counters as $counter) {
            if ($counter['name']===$name) {
                return $counter;
            }
        }
        return null;
    }

    /**
     * GET /counter/{id}/check
     * @param int $id
     * @return array
     * @throws ApiException
     * @link http://api.yandex.ru/metrika/doc/ref/reference/check-counter.xml
     */
    public function checkCounter($id)
    {
        $result = $this->request('GET', "/counter/$id/check.json");
        if (isset($result['counter'])) {
            return $result['counter'];
        } else {
            return null;
        }
    }

    /**
     * Отчет Посещаемость
     * GET /stat/traffic/summary
     * @param int $id
     * @param int $goalId
     * @param datetime|string $dateFrom
     * @param datetime|string $dateTo
     * @param string $group - day, week, month
     * @param int $perPage
     * @return array|null
     * @link http://api.yandex.ru/metrika/doc/ref/stat/traffic-summary.xml
     */
    public function statTrafficSummary($id, $goalId = null, $dateFrom = null, $dateTo = null, $group = null, $perPage = 100)
    {
        $options = array('id'=>$id, 'per_page'=>$perPage);
        if ($goalId !== null)
            $options['goal_id'] = $goalId;
        if ($dateFrom)
            $options['date1'] = self::formatDate($dateFrom);
        if ($dateTo)
            $options['date2'] = self::formatDate($dateTo);
        $options['group'] = $this->checkDictionary($group, 'group');

        return $this->request('GET', '/stat/traffic/summary.json', $options);
    }


    /**
     * Отчет Поисковые системы
     *
     * GET /stat/sources/search_engines
     * @param int $id Идентификатор счетчика (*)
     * @param int $goalId Идентификатор цели счетчика для получения целевого отчета.
     * @param string $dateFrom Дата начала периода выборки в формате YYYYMMDD
     * @param string $dateTo Дата окончания периода выборки в формате YYYYMMDD
     * @param string $tableMode tree ― дерево; plain ― список (используется по умолчанию).
     * @param int $perPage Количество элементов на странице выдачи. По умолчанию выводится 100 записей.
     * @param string $sort Поле данных отчета, по которому необходимо отсортировать результаты запроса.
     *   Значение по умолчанию: visits — результаты запроса сортируются по количеству визитов.
     * @param int $reverse Режим сортировки данных. Возможные значения: 
     *   1 ― по убыванию (используется по умолчанию); 
     *   0 ― по возрастанию.
     * @link http://api.yandex.ru/metrika/doc/ref/stat/sources-search-engines.xml
     */
    public function statSearchEngines($id, $goalId = null, $dateFrom = null, $dateTo = null, $tableMode = "tree", $sort = "visits", $reverse = 1, $perPage = 100)
    {
        $options = array(
            'id'=>$id,);

        !is_null($goalId) ? $options["goal_id"] = $goalId : false;
        !is_null($dateFrom) ? $options["date1"] = date("Ymd", $dateFrom) : false;
        !is_null($dateTo) ? $options["date2"] = date("Ymd", $dateTo) : false;
        !is_null($tableMode) ? $options["table_mode"] = $tableMode : false;
        !is_null($sort) ? $options["sort"] = $sort : false;
        !is_null($reverse) ? $options["reverse"] = $reverse : false;
        !is_null($perPage) ? $options["per_page"] = $perPage : false;

        return $this->request('GET', self::$service . '/stat/sources/search_engines.json', $options);
    }


}
