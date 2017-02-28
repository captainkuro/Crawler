<?php

class Rlsbb_Extractor implements Extractor {
	
	public function can_extract($url) {
		return strpos($url, 'http://www.rlsbb.com') === 0
			|| strpos($url, 'http://rlsbb.com') === 0;
	}

	public function extract($columns, $s, $n, $url) {
		$result = array();
		if (strpos($url, 'http://www.rlsbb.com') === 0) {
			$url = str_replace('http://www.rlsbb.com', 'http://rlsbb.com', $url);
		}
		for ($i=$s; $i<=$n; $i++) {
			$purl = rtrim($url, '/') . '/';
			if ($i > 1) $purl .= 'page/'.$i.'/';
			$p = new Page($purl, array(
				CURLOPT_HTTPHEADER => [
					'Cookie: __cfduid=d9659d2c94757690c1f48ba9fc701336b1488039627; cf_clearance=e6bef035a909c752d7641cf3d971efe49a54fc67-1488298023-1800; PHPSESSID=d8ck2ritjs5ph4amiu48p7mf03',
					'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36',
				],
			));
			// var_dump($p->content());
			$h = new simple_html_dom();
			$h->load($p->content());

			foreach ($h->find('div.post') as $post) {
				$item = array();

				$title_a = $post->find('.postTitle', 0)->find('a', 0);
				$item['link'] = "<a href='{$title_a->href}'>link</a>";

				$title_a = $post->find('.postTitle', 0)->find('a', 0);
				$item['title'] = $title_a->innertext;

				$subtitle = $post->find('.postSubTitle', 0);
				$date = Text::create($subtitle->innertext)->regex_match('/Posted on (.*) in </');
				$date = $date[1];
				$item['date'] = $date;

				$subtitle = $post->find('.postSubTitle', 0);
				$categories = array();
				foreach ($subtitle->find('a[rel=category tag]') as $c) {
					$categories[] = $c->innertext;
				}
				$item['categories'] = implode(', ', $categories);

				$content = $post->find('.postContent', 0);
				if (!$content) {
					$content = $post->find('.entry-content', 0);
				}
				$item['content'] = strip_tags($content->innertext, '<br>');
				$item['description'] = $item['content'];

				$content = $post->find('.postContent', 0);
				if (!$content) {
					$content = $post->find('.entry-content', 0);
				}
				$img = $content->find('img', 0);
				$item['image'] = $img ? $img->outertext() : '';
				$img2 = $content->find('img', 1);
				$item['image2'] = $img2 ? $img2->outertext() : '';
				$img3 = $content->find('img', 2);
				$item['image3'] = $img3 ? $img3->outertext() : '';

				$result[] = $item;
			}
		}
		return $result;
	}
}