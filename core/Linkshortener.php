<?php


class Linkshortener
{
    /** @var Db $db */
    protected $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    /**
     * @param int $length
     * @return string
     * @throws Exception
     */
    public function generateHash( $length = 6 ) : string
    {
        $alphabet = "0123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ";
        $size = strlen($alphabet) - 1;
        $hash = '';
        while ( $length-- ) {
            $hash .= $alphabet[random_int(0, $size)];
        }

        return $hash;
    }

    /**
     * @param string $hash
     * @param string $url
     * @return array
     */
    protected function saveLink(string $hash, string $url) : array
    {
        $this->db->query(
            "INSERT INTO `links` SET `hash` = :hash, `url` = :url",
            [
                'hash' => $hash,
                'url'  => $url
            ]
        );

        if ( $this->db->lastInsertId() ) {
            $result = [
                'success' => true,
            ];
        } else {
            $result = [
                'success' => false,
                'error' => [
                    'message' => 'Не удалось сделать запись в БД',
                ]
            ];
        }

        return $result;
    }

    /**
     * Получить из БД полный урл по хешу
     * @param string $hash
     * @return mixed
     */
    public function getUrlByHash(string $hash)
    {
        $stmt = $this->db->query("SELECT `url` FROM `links` WHERE `hash` = :hash", ['hash' => $hash]);

        return $stmt->fetchColumn();
    }

    /**
     * Получить из БД хеш по полному урл
     * @param string $url
     * @return mixed
     */
    protected function getHashByUrl(string $url)
    {
        $stmt = $this->db->query("SELECT `hash` FROM `links` WHERE `url` = :url", ['url' => $url]);

        return $stmt->fetchColumn();
    }

    /**
     *
     * @param string $url
     * @return bool[]
     * @throws Exception
     */
    public function getShortLink( string $url ): array
    {
        $hash = $this->getHashByUrl( $url );

        if ( empty($hash) ) {
            for ( $i = 5; $i <= 9; $i++ ) {
                $hash = $this->generateHash( $i );

                $checkUrl = $this->getUrlByHash($hash);

                if ( empty($checkUrl) ) break;
            }

            $result = $this->saveLink( $hash, $url );

            if ( !$result['success'] ) {
                return $result;
            }
        }

        return [
            'success' => true,
            'data' => ['hash' => $hash],
        ];
    }
}