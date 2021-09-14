<?php

require_once __DIR__ . '/environment_variables.php';
require_once __DIR__ . '/infra/connection.php';
require_once __DIR__ . '/infra/functions.php';
require_once __DIR__ . '/models/NewsModel.php';

$hosts = array();
$hosts[0] = 'https://www.gov.br/compras/pt-br/acesso-a-informacao/noticias';
$hosts[1] = 'https://www.gov.br/compras/pt-br/acesso-a-informacao/noticias?b_start:int=30';
$hosts[2] = 'https://www.gov.br/compras/pt-br/acesso-a-informacao/noticias?b_start:int=60';
$hosts[3] = 'https://www.gov.br/compras/pt-br/acesso-a-informacao/noticias?b_start:int=90';
$hosts[4] = 'https://www.gov.br/compras/pt-br/acesso-a-informacao/noticias?b_start:int=120';

$captured_news = 0;
$recorded_news = 0;

foreach ($hosts as $host) {
    $return_text = requestCurlHtml($host);

    // Parse ---------------------------------------------------------------
    $dom = new DOMDocument();
    @$dom->loadHTML($return_text);

    $xpath = new DomXPath($dom);

    $date_position = 0;
    $hour_position = 1;
    $news_vet = array();

    $articles = $dom->getElementsByTagName('article');
    $span_content = $xpath->query("//span[@class='summary-view-icon']");

    $i = 0;
    foreach ($articles as $article) {
        $h2_content = $article->getElementsByTagName('h2')->item(0);
        $a_content = $h2_content->getElementsByTagName('a')->item(0);

        // Armazenar os dados em um array -----------------------------------
        $news_vet[$i]['headline'] = trim(
            $h2_content->getElementsByTagName('a')->item(0)->textContent
        );

        $news_vet[$i]['link'] = trim($a_content->getAttribute('href'));

        $date = convertDateBrToDateDb(
            trim($span_content->item($date_position)->nodeValue)
        );

        $hour = convertHourSourceToHourDb(
            trim($span_content->item($hour_position)->nodeValue)
        );

        $news_vet[$i]['news_datetime'] = "$date $hour";
        // FIM - Armazenar os dados em um array -----------------------------

        $i++;
        $date_position = $date_position + 3;
        $hour_position = $hour_position + 3;

        $captured_news++;
    }
    // FIM - Parse ----------------------------------------------------------

    // Gravar -----------------------------------------------------
    $newsModel = new NewsModel();

    foreach ($news_vet as $news) {
        if (!$newsModel->existsNews($conn, $news['news_datetime'], $news['headline'])) {
            $newsModel->create($conn, $news);
            $recorded_news++;
        }
    }
    // FIM - Gravar ------------------------------------------------
}

// Exibir resultados -------------------------------------------
showResults($captured_news, $recorded_news);
// FIM - Exibir resultados -------------------------------------
