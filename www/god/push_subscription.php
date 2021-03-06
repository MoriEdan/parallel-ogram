<?php

	include("../include/init.php");
	loadlib("god");

	if (! $GLOBALS['cfg']['enable_feature_instagram_push']){
		error_disabled();
	}

	loadlib("instagram_push");
	loadlib("instagram_push_subscriptions");

	$id = get_int32("id");
	$sub = instagram_push_subscriptions_get_by_id($id);

	if (! $sub){
		error_404();
	}

	$crumb_key = "delete_feed";
	$GLOBALS['smarty']->assign("crumb_key", $crumb_key);

	if ((post_str("delete") && (crumb_check($crumb_key)))){

		$rsp = instagram_push_unsubscribe($sub);
		$GLOBALS['smarty']->assign_by_ref("delete_feed", $rsp);

		if ($rsp['ok']){
			$rsp = instagram_push_subscriptions_delete($sub);
			$GLOBALS['smarty']->assign_by_ref("delete_sub", $rsp);

			if ($rsp['ok']){
				$url = "{$GLOBALS['cfg']['abs_root_url']}god/push/subscriptions?deleted=1";

				header("location: $url");
				exit();
			}
		}
	}

	$topic_map = instagram_push_topic_map();
	$sub['str_topic'] = $topic_map[$sub['topic_id']];

	if ($sub['last_update_details']){
		$sub['last_update_details'] = json_decode($sub['last_update_details'], "as hash");
	}

	$GLOBALS['smarty']->assign_by_ref("subscription", $sub);

	$GLOBALS['smarty']->display("page_god_push_subscription.txt");
	exit();
?>
