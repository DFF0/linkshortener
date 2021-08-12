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
        while($length--) {
            $hash .= $alphabet[random_int(0, $size)];
        }

        return $hash;
    }

    protected function saveLink($hash, $url)
    {
        $this->db->query(
            "INSERT INTO `links` SET `hash` = :hash, `url` = :url",
            [
                'hash' => $hash,
                'url'  => $url
            ]
        );
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
     * Получить из БД полный урл по хешу
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
     * @return string
     * @throws Exception
     */
    public function getShortLink( string $url ): string
    {
        $hash = $this->getHashByUrl( $url );

        if ( empty($hash) ) {
            $hash = $this->generateHash( 6 );

            $this->saveLink( $hash, $url );
        }

        return $hash;
    }
}