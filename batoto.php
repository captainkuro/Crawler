<?php
// http://www.batoto.com/comic/_/comics/xblade-r789
// http://www.batoto.com/read/_/27286/xblade_ch41_by_twilight-dreams-scans
extract($_POST);

echo 
X::_o('html'),
	X::_o('body')
;
?>
<script type="text/javascript">
var global_check = false;
function click_this() {
    global_check = !global_check;
    var tags = document.getElementsByTagName("input");
    for (i in tags) {
        if (tags[i].type == "checkbox") {
            tags[i].checked = global_check;
        }
    }
}
</script>
<?php

// stage 1
echo 
X::h2('1'),
X::form(array('method'=>'post'),
	'http://www.batoto.com/comic/_/comics/xblade-r789',X::br(),
	'Manga: ',X::input(array('type'=>'text','name'=>'base','value'=>@$base)),X::br(),
	'Prefix: ',X::input(array('type'=>'text','name'=>'prefix','value'=>@$prefix)),X::br(),
	X::input(array('type'=>'submit','name'=>'stage1'))
)
;

// stage 2
if (isset($stage1) || isset($stage2)) {
echo
X::h2('2'),
X::_o('form', array('method'=>'post')),
	'Manga: ',X::input(array('type'=>'text','name'=>'base','value'=>@$base)),X::br(),
	'Prefix: ',X::input(array('type'=>'text','name'=>'prefix','value'=>@$prefix)),X::br(),
	X::div('Choose chapter:'),
	X::input(array('type'=>'checkbox','name'=>'all','onclick'=>'click_this()')),'All',X::br(),
	X::_o('table'),
		X::tr(
			X::th('Chapter Name'),
			X::th('Infix')
		)
;

function print_choice($i, $v) {
	echo
	X::tr(
		X::td(
			X::input(array('type'=>'checkbox','name'=>"info[$i][check]",'value'=>$i)),
			$v['desc'],
			X::input(array('type'=>'hidden','name'=>"info[$i][url]",'value'=>$v['url'])),
			X::input(array('type'=>'hidden','name'=>"info[$i][desc]",'value'=>$v['desc']))
		),
		X::td(
			X::input(array('type'=>'text','name'=>"info[$i][infix]",'value'=>$v['infix']))
		)
	)
	;
}

	if (isset($stage1)) {
		// crawl chapters
		$p = new Page($base);
		$p->go_line('h3 class="maintitle"');
		$list = array();
		do {
			if ($p->curr_line()->contain('book_open.png')) {
				$line = $p->curr_line()->dup();
				$href = $line->dup()->cut_between('href="', '"')->to_s();
				$desc = $line->dup()->cut_between('/>', '</a')->to_s();
				preg_match('/h\.(\d+):?/', $desc, $m);
				$infix = $m[1];
				$list[] = array(
					'url' => $href,
					'desc' => $desc,
					'infix' => $infix,
				);
			}
		} while (!$p->next_line()->contain('</table>'));
		
		foreach ($list as $i => $v) {
			print_choice($i, $v);
		}
	} else {
		// from POST
		foreach ($info as $i => $v) { if (isset($v['check'])) {
			print_choice($i, $v);
		} else {
			unset($info[$i]);
		}}
	}
	
echo
	X::_c('table'),
	X::input(array('type'=>'submit','name'=>'stage2')),
X::_c('form')
;

}

// stage 3
if (isset($stage2)) {
function crawl_page($p) {
	global $prefix, $ifx;
	$p->go_line('id="full_image"');
	$img = $p->next_line(3)->dup()->cut_between('src="', '"')->to_s();
	$iname = urldecode(basename($img));
	// 12 karakter aneh
	if (preg_match('/[0-9a-z]{13}\.\w+$/', $iname)) {
		$iname = preg_replace('/\w{13}\.(\w+)$/', '.$1', $iname);
	}
	echo "<a href='$img'>$prefix-$ifx-$iname</a><br/>\n";
}

	foreach ($info as $v) {
		$ifx = Text::create($v['infix'])->pad(3)->to_s();
		$p = new Page($v['url']);
		// grab list of pages
		$p->go_line('id="page_select"');
		$pages = $p->next_line()->extract_to_array('value="', '"');
		// grab current image
		crawl_page($p);
		
		array_shift($pages);
		foreach ($pages as $purl) {
			$p = new Page($purl);
			crawl_page($p);
		}
	}
}

echo
	X::_c('body'),
X::_c('html')
;
