<?php

use Friendica\App;
use Friendica\Core\Config;

function community_init(App $a) {
	if (! local_user()) {
		unset($_SESSION['theme']);
		unset($_SESSION['mobile-theme']);
	}
}

function community_content(App $a, $update = 0) {

	$o = '';

	if ((Config::get('system','block_public')) && (! local_user()) && (! remote_user())) {
		notice( t('Public access denied.') . EOL);
		return;
	}

	if (Config::get('system','community_page_style') == CP_NO_COMMUNITY_PAGE) {
		notice( t('Not available.') . EOL);
		return;
	}

	require_once("include/bbcode.php");
	require_once('include/security.php');
	require_once('include/conversation.php');


	$o .= '<h3>' . t('Community') . '</h3>';
	if (! $update) {
		nav_set_selected('community');
	}

	if (x($a->data,'search')) {
		$search = notags(trim($a->data['search']));
	} else {
		$search = ((x($_GET,'search')) ? notags(trim(rawurldecode($_GET['search']))) : '');
	}

	// Here is the way permissions work in this module...
	// Only public posts can be shown
	// OR your own posts if you are a logged in member

	$r = community_getitems($a->pager['start'], $a->pager['itemspage']);

	if (! dbm::is_result($r)) {
		info( t('No results.') . EOL);
		return $o;
	}

	$maxpostperauthor = Config::get('system','max_author_posts_community_page');

	if ($maxpostperauthor != 0) {
		$count = 1;
		$previousauthor = "";
		$numposts = 0;
		$s = array();

		do {
			foreach ($r AS $row=>$item) {
				if ($previousauthor == $item["author-link"]) {
					++$numposts;
				} else {
					$numposts = 0;
				}
				$previousauthor = $item["author-link"];

				if (($numposts < $maxpostperauthor) && (sizeof($s) < $a->pager['itemspage'])) {
					$s[] = $item;
				}
			}
			if ((sizeof($s) < $a->pager['itemspage'])) {
				$r = community_getitems($a->pager['start'] + ($count * $a->pager['itemspage']), $a->pager['itemspage']);
			}
		} while ((sizeof($s) < $a->pager['itemspage']) && (++$count < 50) && (sizeof($r) > 0));
	} else {
		$s = $r;
	}
	// we behave the same in message lists as the search module

	$o .= conversation($a, $s, 'community', $update);

        $o .= alt_pager($a, count($r));

	return $o;
}

function community_getitems($start, $itemspage) {
	if (Config::get('system','community_page_style') == CP_GLOBAL_COMMUNITY) {
		return(community_getpublicitems($start, $itemspage));
	}
	$r = qu("SELECT %s
		FROM `thread`
		INNER JOIN `user` ON `user`.`uid` = `thread`.`uid` AND NOT `user`.`hidewall`
		INNER JOIN `item` ON `item`.`id` = `thread`.`iid`
		AND `item`.`allow_cid` = ''  AND `item`.`allow_gid` = ''
		AND `item`.`deny_cid`  = '' AND `item`.`deny_gid`  = ''
		%s AND `contact`.`self`
		WHERE `thread`.`visible` AND NOT `thread`.`deleted` AND NOT `thread`.`moderated`
		AND NOT `thread`.`private` AND `thread`.`wall`
		ORDER BY `thread`.`received` DESC LIMIT %d, %d",
		item_fieldlists(), item_joins(),
		intval($start), intval($itemspage)
	);

	return($r);

}

function community_getpublicitems($start, $itemspage) {

	$r = qu("SELECT %s
		FROM `thread`
		INNER JOIN `item` ON `item`.`id` = `thread`.`iid` %s
		WHERE `thread`.`uid` = 0
		ORDER BY `thread`.`created` DESC LIMIT %d, %d",
		item_fieldlists(), item_joins(),
		intval($start), intval($itemspage)
	);

	return($r);
}
