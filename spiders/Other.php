<?php
/*
What if we generalize all Hentai website into one singular form

Requirements:
- actively updated
- each chapter has tags
- also thumbnails

http://nhentai.net
http://hentaibeer.com/
http://www.doujinlife.com/tags/english/
http://hentaifromhell.org/category/manga-doujinshi/
http://manga.hentai.ms/
http://www.hentai2read.com/
http://thedoujin.com/index.php/categories/index?tags=english -> no update
http://pururin.com/browse/6/10/english.html -> bad server
http://www.perveden.com/en/en-directory/?order=3 -> no thumbs
http://www.mangaray.com/ -> down
 */

class Book extends Model {

	public function samples() {
		// @todo
	}

	public function thumbnails() {
		// @todo
	}

	public function pages() {
		// @todo
	}
}

class Other implements Spider {

	public function get_title() {
		return 'Other spider';
	}

	public function get_db_path() {
		return './sqlite/other.db';
	}

	public function create_database() {
		ORM::get_db()->query('CREATE TABLE `book` (
		  `id` integer NOT NULL CONSTRAINT pid PRIMARY KEY AUTOINCREMENT,
		  `title` varchar NOT NULL,
		  `url` varchar NOT NULL,
		  `date` varchar NOT NULL,
		  `count` integer NOT NULL,
		  `thumb` varchar NOT NULL,
		  `tags` text NOT NULL,
		  `images` text NOT NULL
		)');
	}

	public function action_init() {
		// @todo
	}

	public function action_update() {
		echo '<pre>';
		// @todo
	}

	public function action_search() {
		// @todo
	}

	public function action_view() {
		$id = $_GET['id'];
		// @todo
	}

}
