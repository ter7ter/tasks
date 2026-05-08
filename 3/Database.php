<?php
namespace Counter;

class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $this->pdo = new \PDO("mysql:host=" . DB_HOST . ':' . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    /**
     * Сохраняет информацию о посетителе.
     *
     * @param string $ip
     * @param string|null $city
     * @param string|null $country
     * @param string $ua
     * @param string $url
     * @return void
     */
    public function saveVisit(
        string $ip,
        ?string $city,
        ?string $country,
        string $user_agent,
        string $url
    ): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO visits (ip, city, country, user_agent, url)
            VALUES (INET_ATON(:ip), :city, :country, :user_agent, :url)
        ");

        $stmt->execute([
            ':ip' => $ip,
            ':city' => $city,
            ':country' => $country,
            ':user_agent' => $user_agent,
            ':url' => $url,
        ]);
    }

    /**
     * Возвращет список всех страниц, для которых есть данные.
     *
     * @return array
     */
    public function getUrls(): array
    {
        $stmt = $this->pdo->prepare("SELECT DISTINCT url FROM visits");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Возврашает статистику посетителей по городам.
     *
     * @param string $url
     * @param string|null $dateFrom Начало периода (Y-m-d или Y-m-d H:i:s)
     * @param string|null $dateTo   Конец периода (Y-m-d или Y-m-d H:i:s)
     * @return array
     */
    public function getStatByCity(string $url, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $params = [':url' => $url];
        $dateWhere = '';

        if ($dateFrom) {
            $dateWhere .= ' AND visited_at >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $dateWhere .= ' AND visited_at <= :date_to';
            $params[':date_to'] = $dateTo;
        }

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count_city, city FROM `visits` 
            WHERE url = :url {$dateWhere}
            GROUP BY city
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Возвращает количество посещений за каждый час.
     *
     * @param string $url
     * @param string|null $dateFrom Начало периода (Y-m-d или Y-m-d H:i:s)
     * @param string|null $dateTo   Конец периода (Y-m-d или Y-m-d H:i:s)
     * @return array Массив с ключами hour_label, visit_count
     */
    public function getVisitsByHour(string $url, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $params = [':url' => $url];
        $dateWhere = '';

        if ($dateFrom) {
            $dateWhere .= ' AND visited_at >= :date_from';
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $dateWhere .= ' AND visited_at <= :date_to';
            $params[':date_to'] = $dateTo;
        }

        $sql = "
            SELECT 
                DATE_FORMAT(visited_at, '%Y-%m-%d %H:00') AS hour_label,
                COUNT(*) AS visit_count
            FROM visits
            WHERE url = :url {$dateWhere}
            GROUP BY DATE_FORMAT(visited_at, '%Y-%m-%d %H:00') ORDER BY hour_label
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Получение минимальной и максимальной даты для статистики по URL
     * @param string $url
     * @return array
     */
    public function getStartEndDates(string $url): array
    {
        //Даты берём с точностью до часа, но к последней прибавляем ещё час т.к. иначе не войдут последние записи
        $stmt = $this->pdo->prepare("SELECT 
                DATE_FORMAT(min(visited_at), '%Y-%m-%d %H:00') AS start_date,
                DATE_FORMAT(DATE_ADD(max(visited_at), INTERVAL 1 HOUR), '%Y-%m-%d %H:00') AS end_date
            FROM `visits` 
            WHERE url = :url");
        $stmt->execute(['url' => $url]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return [$result['start_date'], $result['end_date']];
    }
}