<?php
mt_srand();
$index = mt_rand(1, 9999);
$validate = str_replace(array('"', "'"), array('\"', "\'"), __('validate', true, true));
$theme = isset($_GET['theme']) ? $_GET['theme'] : $tpl['option_arr']['o_theme'];
?>
<div id="pjWrapperShoppingCart_<?php echo $theme;?>">
	<div id="scContainer_<?php echo $index; ?>" class="scContainer"></div>
</div>
<div class="footer">
    <span id="siteseal"><script async type="text/javascript" src="https://seal.godaddy.com/getSeal?sealID=aShIgU32Tpc6irkhJYSE9pbtoyYxHoi1Jko775qG7URlV02el432vQMcbz0t"></script></span>
    <p>Managed By  PM/FF - 2016</p>
</div>
<script type="text/javascript">
var pjQ = pjQ || {},
	ShoppingCart_<?php echo $index; ?>;
(function () {
	"use strict";
	var isSafari = /Safari/.test(navigator.userAgent) && /Apple Computer/.test(navigator.vendor),

	loadCssHack = function(url, callback){
		var link = document.createElement('link');
		link.type = 'text/css';
		link.rel = 'stylesheet';
		link.href = url;
		document.getElementsByTagName('head')[0].appendChild(link);

		var img = document.createElement('img');
		img.onerror = function(){
			if (callback && typeof callback === "function") {
				callback();
			}
		};
		img.src = url;
	},
	loadRemote = function(url, type, callback) {
		if (type === "css" && isSafari) {
			loadCssHack(url, callback);
			return;
		}
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					if (callback && typeof callback === "function") {
						callback();
					}
				}
			};
		} else {
			element.onload = function () {
				if (callback && typeof callback === "function") {
					callback();
				}
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	},
	options = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_URL; ?>",
		index: <?php echo $index; ?>,
		locale: <?php echo isset($_GET['locale']) && (int) $_GET['locale'] > 0 ? (int) $_GET['locale'] : $controller->pjActionGetLocale(); ?>,
		hide: <?php echo isset($_GET['hide']) && (int) $_GET['hide'] === 1 ? 1 : 0; ?>,
		seoUrl: <?php echo (int) $tpl['option_arr']['o_seo_url']; ?>,
		validate: <?php echo pjAppController::jsonEncode($validate); ?>,
		currency: "<?php echo $tpl['option_arr']['o_currency']; ?>",
		currencysign: "<?php echo pjUtil::getCurrencySign($tpl['option_arr']['o_currency'], false); ?>",
		layout: <?php echo isset($_GET['layout']) && in_array($_GET['layout'], $controller->getLayoutRange()) ? (int) $_GET['layout'] : (int) $tpl['option_arr']['o_layout']; ?>,
		theme: "<?php echo $theme; ?>",
		cid: <?php echo isset($_GET['category_id']) && (int) $_GET['category_id'] > 0 ? $_GET['category_id'] : 0; ?>
	};
	loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>pjQ/pjQuery.js", function () {
		loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>pjQ/pjQuery.validate.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>pjQ/pjQuery.bootstrap.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . PJ_LIBS_PATH; ?>pjQ/fancybox/pjQuery.fancybox.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjShoppingCart.js", function () {
						ShoppingCart_<?php echo $index; ?> = new ShoppingCart(options);
					});
				});
			});
		});
	});
})();
</script>