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

}
