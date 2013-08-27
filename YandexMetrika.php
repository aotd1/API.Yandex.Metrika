<?php
/**
 * Yandex.Metrika API wrapper.
 */
class YandexMetrika extends YandexApiBase
{
    protected static $service = 'https://api-metrika.yandex.ru';

    /**
     * Список доступных счетчиков
     * GET /counters
     * @param string $type - simple, partner
     * @param string $permission - ownn, view, edit
     * @param string $ulogin
     * @param array $field - array of mirrors, goals, filters, operations, grants
     * @return mixed
     * @throws YandexApiException
     * @link http://api.yandex.ru/metrika/doc/ref/reference/get-counter-list.xml
     */
    public function getCounters($type = null, $permission = null, $ulogin = null, array $field = array())
    {
        if (!empty($type) && !in_array($type, array('simple', 'partner')))
            throw new YandexApiException("Unsupported type value: '$type'");
        else
            $options['type'] = $type;

        if (!empty($permission) && !in_array($permission, array('own', 'view', 'edit')))
            throw new YandexApiException("Unsupported permission value: '$permission'");
        else
            $options['permission'] = $permission;

        if (!empty($ulogin))
            $options['ulogin'] = $ulogin;

        if (!empty($field)) {
            foreach ($field as $item)
                if (!in_array($item, array('mirrors', 'goals', 'filters', 'operations', 'grants')))
                    throw new YandexApiException("Unsupported field value: '$item'");
            $options['field'] = implode(',', $field);
        }
        return $this->request('GET', self::$service . '/counters.json', $options);
    }

    /**
     * POST /counters
     */
    public function addCounter($params)
    {

    }

    /**
     * DELETE /counter/{id}
     */
    public function deleteCounter($id)
    {

    }

    /**
     * Отчет Посещаемость
     * GET /stat/traffic/summary
     * @param int $id
     * @param int $goalId
     * @param timestamp $dateFrom
     * @param timestamp $dateTo
     * @param string $group - day, week, month
     * @param int $perPage
     * @link http://api.yandex.ru/metrika/doc/ref/stat/traffic-summary.xml
     */
    public function statTrafficSummary($id, $goalId = null, $dateFrom = null, $dateTo = null, $group = null, $perPage = 100)
    {

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
