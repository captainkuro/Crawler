<?php
require '../class/simple_html_dom.php';
require '../class/text.php';
require '../class/page.php';

function get_all_codes() {
	$raw = file_get_contents('Daftar Emiten Dan Kode Saham Di Bursa Efek Indonesia.html');
	$h = new simple_html_dom();
	$h->load($raw);

	$table = $h->find('.font_general', 0);
	$trs = $table->find('tr[bgcolor="#F4F4F4"]');
	$result = [];
	foreach ($trs as $tr) {
		$code = trim($tr->find('td', 1)->text());
		$name = trim($tr->find('td', 2)->text());

		$sector = $tr->find('td', 3)->text();
		$parts = explode('|', $sector);
		$sector = trim(array_shift($parts));
		$index = implode(',', array_map('trim', $parts));
		
		$result[] = [
			'code' => $code, 'name' => $name, 
			'sector' => $sector, 'index' => $index
		];
	}
	return $result;
}

// $x = get_all_codes();
// file_put_contents('all_codes.out', var_export($x, true));

function extract_fin($text) {
	$text = preg_replace('#\+\s+<td#', '</th><td', $text);
	$h = new simple_html_dom();
	$h->load($text);

	$table = $h->find('.align07', 0);
	$periodTr = $table->find('tr', 1);
	$periods = [];

	for ($i=0; $i<=3; $i++) {
		$td = $periodTr->find('td', $i);
		if (trim($td->text())) {
			$periods[] = trim($td->text());
		}
	}

	$row = 2;
	$result = [];
	while ($dataTr = $table->find('tr', $row++)) {
		$label = trim($dataTr->find('th', 0)->text());
		for ($i=0; $i<=3; $i++) {
			$td = $dataTr->find('td', $i);
			$amount = trim($td->text());
			if (isset($periods[$i])) $result[$periods[$i]][$label] = $amount;
		}
	}
	return $result;
}
// $text = file_get_contents('view-source_dwsec-id.com_hmpg_quote_quoteMain-finan.do.html');
// $x = extract_fin($text);
// print_r($x);

function fetch_html($code, $year) {
	$url = 'http://dwsec-id.com/hmpg/quote/quoteMain-finan.do';
	$body = "tabQuoteFlag=01&tabClickYn=Y&loadM_01=2&searchQuart=3&searchYear={$year}&stcd={$code}";
	$p = new Page($url, [
		CURLOPT_POST => 1,
		CURLOPT_POSTFIELDS => $body,
	]);
	return $p->content();
}
// $text = fetch_html('AALI', 2016);
// print_r(extract_fin($text));

$codes = get_all_codes();
$year = 2016;
$result = [];
foreach ($codes as $row) {
	$code = $row['code'];
	echo "$code $year\n";
	$text = fetch_html($code, $year);
	$result[$code] = extract_fin($text);
}
file_put_contents('finan_2016.out', var_export($result, true));
