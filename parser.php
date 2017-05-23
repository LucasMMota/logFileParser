<?php

/**
 * Classe para fazer o parse do arquivo de Log e retornar informações
 * Created by PhpStorm.
 * User: Lucas.Fonseca
 * Date: 5/22/17
 * Time: 3:16 PM
 */
class Parser
{
    private $arrWebhooksStatusCount = array();
    private $arrUrlsCallCount = array();

    /**
     * Parser constructor.
     * Abre e lê o arquivo, contabilizando as informações
     */
    public function __construct()
    {
        $logFile = fopen("log.txt", "r");

        while (!feof($logFile)) {
            $linha = fgets($logFile);
            $rule = 'level=info response_body=".*?" request_to="(.*?)" response_headers=.*? response_status="(.*?)"';

            //Checa se a linha tem as infs de webhook
            if (preg_match("/{$rule}/", $linha, $match)) {

                // incrementa a quantidade de chamadas para a URL
                if (!isset($this->arrUrlsCallCount[$match[1]])) {
                    $this->arrUrlsCallCount[$match[1]] = 0;
                }
                $this->arrUrlsCallCount[$match[1]] += 1;

                // incrementa a quantidade de webhooks por status
                if (!isset($this->arrWebhooksStatusCount[$match[2]])) {
                    $this->arrWebhooksStatusCount[$match[2]] = 0;
                }
                $this->arrWebhooksStatusCount[$match[2]] += 1;
            }
        }

        fclose($logFile);

        $this->getTopUrls();
    }

    /**
     * Retorna o array de urls ordenados por quantidade de chamadas
     *
     * @param int $n
     * @return array
     */
    private function getTopUrls($n = 3)
    {
        arsort($this->arrUrlsCallCount);

        $arrReturn = array();
        $count = 0;
        foreach ($this->arrUrlsCallCount as $k => $v) {
            if ($count++ < $n)
                $arrReturn[$k] = $v;
        }
        return $arrReturn;
    }

    /**
     * Retorna o array de Satus Code e quantidades ordenados pela quantidade
     *
     * @return array
     */
    private function getArrWebhooksStatusCount()
    {
        arsort($this->arrWebhooksStatusCount);
        return $this->arrWebhooksStatusCount;
    }

    /**
     * Imprime a mensagem do teste
     *
     */
    public function printMessages()
    {
        echo "URLs mais chamadas:\n";
        foreach ($this->getTopUrls() as $url => $n) {
            echo "$url - $n \n";
        }

        echo "\n";
        echo "Quantidade de Webhooks por status:\n";
        foreach ($this->getArrWebhooksStatusCount() as $url => $n) {
            echo "$url - $n \n";
        }
    }
}

// Execução
$fileParser = new Parser;
$fileParser->printMessages();