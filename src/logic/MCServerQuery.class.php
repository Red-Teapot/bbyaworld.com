<?php

class MCServerQuery {
    const F_MAGIC = "\xFE\xFD";
    const F_TYPE_HANDSHAKE = "\x09";
    const F_TYPE_STAT = "\x00";

    const F_SESSION_ID = "\x01\x02\x03\x04";

    private $log;

    function __construct($log) {
        $this->log = $log;
    }

    private static function getChallengeTokenString($challengeToken) {
        $challengeTokenString = "";
        $challengeTokenString .= chr($challengeToken >> 24 & 0xFF);
        $challengeTokenString .= chr($challengeToken >> 16 & 0xFF);
        $challengeTokenString .= chr($challengeToken >>  8 & 0xFF);
        $challengeTokenString .= chr($challengeToken       & 0xFF);

        return $challengeTokenString;
    }

    private function generateHandshake() {
        return self::F_MAGIC . self::F_TYPE_HANDSHAKE . self::F_SESSION_ID;
    }

    private function generateFullStat($challengeToken) {
        return self::F_MAGIC . self::F_TYPE_STAT . self::F_SESSION_ID . $this->getChallengeTokenString($challengeToken) . "\x00\x00\x00\x00";
    }

    private function getChallengeToken($handshake) {
        return intval(substr($handshake, 5, strlen($handshake) - 1));
    }

    public function getPlayers($address, $port, $timeout) {
        if($this->log)
            $this->log->debug("Getting players of " . $address . ":" . $port);

        $sock = stream_socket_client("udp://" . $address . ":" . $port, $errno, $errstr, $timeout);

        if(!$sock) {
            $this->log->error("Error creating socket: " . $errno . ": " . $errstr);
            return false;
        }
        if($this->log)
            $this->log->debug("Socket created");

        stream_set_timeout($sock, $timeout);

        if($this->log)
            $this->log->debug("Socket timeout set");

        $players = array();

        fwrite($sock, $this->generateHandshake());

        if($this->log)
            $this->log->debug("Handshake sent");

        $response = fread($sock, 256);
        $metadata = stream_get_meta_data($sock);
        if($metadata["timed_out"]) {
            $this->log->error("Reading timed out");
            return false;
        }

        $challengeToken = $this->getChallengeToken($response);

        if($this->log)
            $this->log->debug("Challenge token is " . $challengeToken);

        fwrite($sock, $this->generateFullStat($challengeToken));

        $response = fread($sock, 4096);

        $responseSections = explode("\x00\x00\x01player_\x00\x00", $response);
        $playersSection = $responseSections[1];
        $playersRaw = explode("\x00", $playersSection);

        foreach ($playersRaw as $player) {
            if(!empty($player))
                $players[] = $player;
        }

        fclose($sock);
        if($this->log)
            $this->log->debug("Socket closed");
        return $players;
    }
}
