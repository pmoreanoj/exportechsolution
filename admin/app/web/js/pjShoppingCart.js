/*!
 * Shopping Cart v4.2
 * http://phpjabbers.com/shopping-cart/
 * 
 * Copyright 2015, StivaSoft Ltd.
 * 
 * Date: Wed May 06 10:57:28 2012 +0300
 */
(function (window, undefined){
	"use strict";
	
	pjQ.$.ajaxSetup({
		xhrFields: {
			withCredentials: true
		}
	});
	
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined),
		fancybox = (pjQ.$.fn.fancybox !== undefined),
		dialog = (pjQ.$.fn.dialog !== undefined),
		routes = [
		          {pattern: /^#!\/Cart$/, eventName: "loadCart"},
		          {pattern: /^#!\/Checkout$/, eventName: "loadCheckout"},
		          {pattern: /^#!\/Preview$/, eventName: "loadPreview"},
		          {pattern: /^#!\/Favs$/, eventName: "loadFavs"},
		          {pattern: /^#!\/Login$/, eventName: "loadLogin"},
		          {pattern: /^#!\/Forgot$/, eventName: "loadForgot"},
		          {pattern: /^#!\/Register$/, eventName: "loadRegister"},
		          {pattern: /^#!\/Profile$/, eventName: "loadProfile"},
		          {pattern: /^#!\/Product\/(\d+)$/, eventName: "loadProduct"},
		          {pattern: /^#!\/.*-(\d+)\.html$/, eventName: "loadProduct"},
		          {pattern: /^#!\/Products$/, eventName: "loadProducts"},
		          {pattern: /^#!\/Products\/q:(.*)?\/category:(\d+)?\/page:(\d+)?$/, eventName: "loadProducts"}
		          ];
	
	function log() {
		if (window.console && window.console.log) {
			for (var x in arguments) {
				if (arguments.hasOwnProperty(x)) {
					window.console.log(arguments[x]);
				}
			}
		}
	}
	
	function assert() {
		if (window && window.console && window.console.assert) {
			window.console.assert.apply(window.console, arguments);
		}
	}
	
	function hashBang(value) {
		if (value !== undefined && value.match(/^#!\//) !== null) {
			if (window.location.hash == value) {
				return false;
			}
			window.location.hash = value;
			return true;
		}
		
		return false;
	}
	
	function onHashChange() {
		var i, iCnt, m;
		for (i = 0, iCnt = routes.length; i < iCnt; i++) {
			m = window.location.hash.match(routes[i].pattern);
			if (m !== null) {
				pjQ.$(window).trigger(routes[i].eventName, m.slice(1));
				break;
			}
		}
		if (m === null) {
			pjQ.$(window).trigger("loadProducts");
		}
	}
	
	pjQ.$(window).on("hashchange", function (e) {
    	onHashChange.call(null);
    });
	
	function ShoppingCart(options) {
		if (!(this instanceof ShoppingCart)) {
			return new ShoppingCart(options);
		}
				
		this.reset.call(this);
		this.init.call(this, options);
		
		return this;
	}
	
	ShoppingCart.inObject = function (val, obj) {
		var key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				if (obj[key] == val) {
					return true;
				}
			}
		}
		return false;
	};
	
	ShoppingCart.size = function(obj) {
		var key,
			size = 0;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) {
				size += 1;
			}
		}
		return size;
	};
	
	ShoppingCart.compare = function(obj1, obj2) {
		var p;
		for (p in obj1) {
			if (obj2[p] === undefined) {
				return false;
			}
		}
		for (p in obj1) {
			if (obj1[p]) {
				switch (typeof(obj1[p])) {
					case 'object':
						if (!obj1[p].equals(obj2[p])) {
							return false;
						}
						break;
					case 'function':
						if (obj2[p] === undefined || (p != 'equals' && obj1[p].toString() != obj2[p].toString())) {
							return false;
						}
						break;
	              default:
	                  if (obj1[p] != obj2[p]) {
	                	  return false;
	                  }
				}
			} else {
				if (obj2[p])
				{
					return false;
				}
			}
		}

		for (p in obj2) {
			if (obj1[p] === undefined) {
				return false;
			}
		}

		return true;
	};
	
	ShoppingCart.prototype = {
		reset: function () {
			this.$container = null;
			this.container = null;
			this.page = null;
			this.q = null;
			this.category_id = null;
			this.product_id = null;
			//Product
			this.stockObj = {};
			this.stockIds = {};
			this.qtyObj = {};
			this.priceObj = {};
			this.price = 0.00;
			this.priceStocks = 0;
			this.priceExtras = 0;
			this.unitPrice = 0;
			this.qty = 1;
			this.canBuy = true;
			//--Product
			this.options = {};
			
			return this;
		},
		getLogin: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionLogin"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				
				if (validate) {
					if(self.options.layout != '3')
					{
						var $form = self.$container.find(".scSelectorLoginForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": {
									required: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionLogin"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										hashBang("#!/Products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}else{
						var $form = self.$container.find(".scSelectorLoginForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": {
									required: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent(),
									$input_group = $parent.parent();
								error.insertAfter(element.parent());
								$input_group.addClass('has-error');
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								$parent.removeClass('has-error').addClass('has-success');
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionLogin"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										hashBang("#!/Products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}
			});
		},
		getLogout: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFront&action=pjActionLogout"].join("")).done(function (data) {
				if (window.location.hash.match(/#!\/Products\/q:/) === null) {
					hashBang("#!/Products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
				} else {
					hashBang("#!/Products");
				}
			});
		},
		getForgot: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionForgot"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				
				if (validate) {
					if(self.options.layout != '3')
					{
						var $form = self.$container.find(".scSelectorForgotForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionForgot"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeError")
											.addClass("scNoticeSuccess")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
									} else if (data.status == "ERR") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}else{
						var $form = self.$container.find(".scSelectorForgotForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent(),
									$input_group = $parent.parent();
								error.insertAfter(element.parent());
								$input_group.addClass('has-error');
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								$parent.removeClass('has-error').addClass('has-success');
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionForgot"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-warning")
											.addClass("alert-success")
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
									} else if (data.status == "ERR") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}
			});
		},
		getProfile: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProfile"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				
				if (validate) {
					if(self.options.layout != '3')
					{
						var $form = self.$container.find(".scSelectorProfileForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required"
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProfile"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeError")
											.addClass("scNoticeSuccess")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}else{
						var $form = self.$container.find(".scSelectorProfileForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required"
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent();
								if(element.attr('name') == 'captcha')
								{
									error.insertAfter(element.parent().parent());
								}else{
									if($parent.hasClass('input-group'))
									{
										error.insertAfter(element.parent());
									}else{
										error.insertAfter(element);
									}
								}
								if($parent.hasClass('input-group'))
								{
									var $input_group = $parent.parent();
									$input_group.addClass('has-error');
								}else{
									if(element.attr('name') != 'captcha')
									{
										$parent.addClass('has-error');
									}else{
										$parent.parent().parent().addClass('has-error');
									}
								}
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								if($sibling.attr('name') == 'captcha')
								{
									$parent.parent().parent().removeClass('has-error').addClass('has-success');
								}else{
									$parent.removeClass('has-error').addClass('has-success');
								}
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProfile"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-warning")
											.addClass("alert-success")
											.show();
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}
			});
		},
		getRegister: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionRegister"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				
				if (validate) {
					if(self.options.layout != '3')
					{
						var $form = self.$container.find(".scSelectorRegisterForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required",
								"captcha": {
									required: true,
									minlength: 6,
									maxlength: 6,
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha"
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"captcha": {
									required: $form.find("input[name='captcha']").attr('data-err'),
									remote: $form.find("input[name='captcha']").attr('data-captcha')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							errorPlacement: function (error, element) {
								error.insertAfter(element);
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionRegister"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeError")
											.addClass("scNoticeSuccess")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
										var $captcha = $form.find(".scSelectorCaptcha").eq(0);
										$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								
								return false;
							}
						});
					}else{
						var $form = self.$container.find(".scSelectorRegisterForm");
						$form.validate({
							rules: {
								"email": {
									required: true,
									email: true
								},
								"password": "required",
								"captcha": {
									required: true,
									minlength: 6,
									maxlength: 6,
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha"
								}
							},
							messages: {
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"password": $form.find("input[name='password']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"captcha": {
									required: $form.find("input[name='captcha']").attr('data-err'),
									remote: $form.find("input[name='captcha']").attr('data-captcha')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent();
								if(element.attr('name') == 'captcha')
								{
									error.insertAfter(element.parent().parent());
								}else{
									if($parent.hasClass('input-group'))
									{
										error.insertAfter(element.parent());
									}else{
										error.insertAfter(element);
									}
								}
								if($parent.hasClass('input-group'))
								{
									var $input_group = $parent.parent();
									$input_group.addClass('has-error');
								}else{
									if(element.attr('name') != 'captcha')
									{
										$parent.addClass('has-error');
									}else{
										$parent.parent().parent().addClass('has-error');
									}
								}
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								if($sibling.attr('name') == 'captcha')
								{
									$parent.parent().parent().removeClass('has-error').addClass('has-success');
								}else{
									$parent.removeClass('has-error').addClass('has-success');
								}
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionRegister"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-warning")
											.addClass("alert-success")
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
										var $captcha = $form.find(".scSelectorCaptcha").eq(0);
										$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});

								return false;
							}
						});
					}
				}
			});
		},
		disableButtons: function () {
			this.$container.find(".scSelectorButton").attr("disabled", "disabled");
		},
		enableButtons: function () {
			this.$container.find(".scSelectorButton").removeAttr("disabled");
		},
		addToFavs: function () {
			var self = this,
				qs = this.buildQueryString.call(this);
			if (!qs) {
				log("Stock Id not set");
				return;
			}
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionAdd"].join(""), qs).done(function (data) {
				hashBang("#!/Favs");
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		removeFromFavs: function (hash) {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionRemove"].join(""), {
				"hash": hash
			}).done(function (data) {
				self.viewFavs.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		emptyFavs: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionEmpty"].join("")).done(function (data) {
				self.viewFavs.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		viewFavs: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionFavs"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
			});
		},
		checkFavs: function () {
			var self = this,
				qs = this.buildQueryString.call(this);

			if (!qs) {
				self.$container.find(".scButtonAdd2Favs").removeClass("scButtonAdd2FavsIn");
			} else {
				pjQ.$.post([this.options.folder, "index.php?controller=pjFrontFavs&action=pjActionCheck"].join(""), qs).done(function (data) {
					switch (data.status) {
					case "OK":
						self.$container.find(".scButtonAdd2Favs").addClass("scButtonAdd2FavsIn");
						break;
					case "ERR":
						self.$container.find(".scButtonAdd2Favs").removeClass("scButtonAdd2FavsIn");
						break;
					}
				});
			}
		},
		removeCode: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionRemoveCode"].join("")).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		buildQueryString: function () {
			var m, $el, qs, i, iCnt, productObj = {},
				$form = this.$container.find(".scSelectorProductForm"),
				attr = $form.find(".scSelectorAttr").serializeArray(),
				qs = $form.serialize();
			
			for (i = 0, iCnt = attr.length; i < iCnt; i++) {
				m = attr[i].name.match(/attr\[(\d+)\]/);
				productObj[m[1]] = attr[i].value; 
			}
			
			for (i = 0, iCnt = this.stockObj.length; i < iCnt; i++) {
				if (ShoppingCart.compare(productObj, this.stockObj[i])) {
					qs += "&stock_id=" + this.stockIds[i];
					return qs;
					break;
				}
			}
			
			// Apply only if product have not attributes
			if (this.stockObj.length === 0 && this.stockIds[0]) {
				return [qs, "&stock_id=", this.stockIds[0]].join("");
			}
			
			return false;
		},
		getAddress: function (el) {
			var self = this,
				$el = pjQ.$(el),
				address_id = $el.find("option:selected").val(),
				elName = $el.attr("name");
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionGetAddress"].join(""), {
				"id": address_id
			}).done(function (data) {
				var $form = self.$container.find(".scSelectorCheckoutForm");
				if (data.status === "OK") {
					data = data.result;
					if (elName == 'b_address_id') {
						$form.find("input[name='b_name']").val(data.name).attr("data-original", data.name);
						$form.find("select[name='b_country_id']").val(data.country_id).attr("data-original", data.country_id);
						$form.find("input[name='b_state']").val(data.state).attr("data-original", data.state);
						$form.find("input[name='b_city']").val(data.city).attr("data-original", data.city);
						$form.find("input[name='b_zip']").val(data.zip).attr("data-original", data.zip);
						$form.find("input[name='b_address_1']").val(data.address_1).attr("data-original", data.address_1);
						$form.find("input[name='b_address_2']").val(data.address_2).attr("data-original", data.address_2);
						
						pjQ.$(window).trigger("reloadAddress", {type: "billing"});
						self.$container.find(".scSelectorSaveB").hide();
					} else if (elName == 's_address_id') {
						$form.find("input[name='s_name']").val(data.name).attr("data-original", data.name);
						$form.find("select[name='s_country_id']").val(data.country_id).attr("data-original", data.country_id);
						$form.find("input[name='s_state']").val(data.state).attr("data-original", data.state);
						$form.find("input[name='s_city']").val(data.city).attr("data-original", data.city);
						$form.find("input[name='s_zip']").val(data.zip).attr("data-original", data.zip);
						$form.find("input[name='s_address_1']").val(data.address_1).attr("data-original", data.address_1);
						$form.find("input[name='s_address_2']").val(data.address_2).attr("data-original", data.address_2);
						$form.find("input[name='same_as']").prop("checked", false);
						$form.find(".scSelectorBoxShipping").show();
						
						pjQ.$(window).trigger("reloadAddress", {type: "shipping"});
						self.$container.find(".scSelectorSaveS").hide();
					}
				} else {
					if (elName == 'b_address_id') {
						$form.find("input[name='b_name'], select[name='b_country_id'], input[name='b_state'], input[name='b_city'], input[name='b_zip'], input[name='b_address_1'], input[name='b_address_2']").val("");
						
						pjQ.$(window).trigger("reloadAddress", {type: "billing"});
						self.$container.find(".scSelectorSaveB").show();
					} else if (elName == 's_address_id') {
						$form.find("input[name='s_name'], select[name='s_country_id'], input[name='s_state'], input[name='s_city'], input[name='s_zip'], input[name='s_address_1'], input[name='s_address_2']").val(""); 
						$form.find("input[name='same_as']").prop("checked", false);
						$form.find(".scSelectorBoxShipping").show();
						
						pjQ.$(window).trigger("reloadAddress", {type: "shipping"});
						self.$container.find(".scSelectorSaveS").show();
					}
				}
			}).always(function () {
				self.enableButtons.call(self);
			});
		},
		addToCart: function (qs) {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionAdd"].join(""), qs).done(function (data) {
				hashBang("#!/Cart");
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		removeFromCart: function (hash) {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionRemove"].join(""), {
				"hash": hash
			}).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		emptyCart: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionEmpty"].join("")).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		updateCart: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.post([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionUpdate"].join(""), this.$container.find(":input[name^='qty'], select[name='tax_id']").serialize()).done(function (data) {
				self.viewCart.call(self);
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		viewCart: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCart"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				if (validate) {
					var $form = self.$container.find(".scSelectorVoucherForm");
					$form.validate({
						rules: {
							"code": "required"
						},
						messages: {
							"code": $form.find("input[name='code']").attr('data-err')
						},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "scError",
						validClass: "scValid",
						submitHandler: function (form) {
							self.disableButtons.call(self);
							var $form = pjQ.$(form);
							pjQ.$.post([self.options.folder, "index.php?controller=pjFrontCart&action=pjActionApplyCode"].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									self.viewCart.call(self);
								} else if (data.status == "ERR") {
									$form
										.find(".scSelectorNoticeMsg")
										.html(data.text)
										.removeClass("scNoticeSuccess")
										.addClass("scNoticeError")
										.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
										.show();
									self.enableButtons.call(self);
								}
							}).fail(function () {
								self.enableButtons.call(self);
							});
							return false;
						}
					});
					var $form = self.$container.find(".scSelectorCartForm");
					$form.validate({
						messages: {
							"tax_id": $form.find("select[name='tax_id']").attr('data-err')
						},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "scError",
						validClass: "scValid",
						submitHandler: function (form) {
							self.disableButtons.call(self);
							hashBang("#!/Checkout");
							return false;
						}
					});
				}
			});
		},
		checkoutCart: function () {
			var self = this;
			this.disableButtons.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				pjQ.$('.modal-dialog').css("z-index", "9999"); 
				pjQ.$(window).trigger("reloadAddress", {type: "billing"});
				pjQ.$(window).trigger("reloadAddress", {type: "shipping"});
				
				if (validate) {
					if(self.options.layout != '3')
					{
						var $form = self.$container.find(".scSelectorCheckoutForm");
						$form.validate({
							rules: {
								"captcha" : {
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha",
									required: true,
									minlength: 6,
									maxlength: 6
								}
							},
							messages: {
								"b_name": $form.find("input[name='b_name']").attr('data-err'),
								"b_country_id": $form.find("select[name='b_country_id']").attr('data-err'),
								"b_city": $form.find("input[name='b_city']").attr('data-err'),
								"b_state": $form.find("input[name='b_state']").attr('data-err'),
								"b_zip": $form.find("input[name='b_zip']").attr('data-err'),
								"b_address_1": $form.find("input[name='b_address_1']").attr('data-err'),
								"b_address_2": $form.find("input[name='b_address_2']").attr('data-err'),
								"s_name": $form.find("input[name='s_name']").attr('data-err'),
								"s_country_id": $form.find("input[name='s_country_id']").attr('data-err'),
								"s_city": $form.find("input[name='s_city']").attr('data-err'),
								"s_state": $form.find("input[name='s_state']").attr('data-err'),
								"s_zip": $form.find("input[name='s_zip']").attr('data-err'),
								"s_address_1": $form.find("input[name='s_address_1']").attr('data-err'),
								"s_address_2": $form.find("input[name='s_address_2']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"payment_method":  $form.find("select[name='payment_method']").attr('data-err'),
								"notes": $form.find("textarea[name='notes']").attr('data-err'),
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"captcha": {
									remote: $form.find("input[name='captcha']").attr('data-captcha'),
									required: $form.find("input[name='captcha']").attr('data-err'),
								},
								"terms": $form.find("input[name='terms']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										hashBang("#!/Preview");
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}else{
						var $form = self.$container.find(".scSelectorCheckoutForm");
						$form.validate({
							rules: {
								"captcha" : {
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha",
									required: true,
									minlength: 6,
									maxlength: 6
								}
							},
							messages: {
								"b_name": $form.find("input[name='b_name']").attr('data-err'),
								"b_country_id": $form.find("select[name='b_country_id']").attr('data-err'),
								"b_city": $form.find("input[name='b_city']").attr('data-err'),
								"b_state": $form.find("input[name='b_state']").attr('data-err'),
								"b_zip": $form.find("input[name='b_zip']").attr('data-err'),
								"b_address_1": $form.find("input[name='b_address_1']").attr('data-err'),
								"b_address_2": $form.find("input[name='b_address_2']").attr('data-err'),
								"s_name": $form.find("input[name='s_name']").attr('data-err'),
								"s_country_id": $form.find("input[name='s_country_id']").attr('data-err'),
								"s_city": $form.find("input[name='s_city']").attr('data-err'),
								"s_state": $form.find("input[name='s_state']").attr('data-err'),
								"s_zip": $form.find("input[name='s_zip']").attr('data-err'),
								"s_address_1": $form.find("input[name='s_address_1']").attr('data-err'),
								"s_address_2": $form.find("input[name='s_address_2']").attr('data-err'),
								"client_name": $form.find("input[name='client_name']").attr('data-err'),
								"phone": $form.find("input[name='phone']").attr('data-err'),
								"url": $form.find("input[name='url']").attr('data-err'),
								"payment_method":  $form.find("select[name='payment_method']").attr('data-err'),
								"notes": $form.find("textarea[name='notes']").attr('data-err'),
								"email": {
									required: $form.find("input[name='email']").attr('data-err'),
									email: $form.find("input[name='email']").attr('data-email')
								},
								"captcha": {
									remote: $form.find("input[name='captcha']").attr('data-captcha'),
									required: $form.find("input[name='captcha']").attr('data-err'),
								},
								"terms": $form.find("input[name='terms']").attr('data-err')
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorPlacement: function (error, element) {
								var $parent = element.parent(),
									$input_group = $parent.parent();
								if(element.attr('name') == 'terms')
								{
									error.insertAfter(element.parent());
								}else{
									error.insertAfter(element);
								}
								if(element.attr('name') == 'captcha')
								{
									$input_group.parent().addClass('has-error');
								}else{
									$parent.addClass('has-error');
								}
							},
							success: function (label) {
								var $parent = pjQ.$(label).parent(),
									$sibling = pjQ.$(label).siblings();
								if($sibling.attr('name') == 'captcha')
								{
									$parent.parent().parent().removeClass('has-error').addClass('has-success');
								}else{
									$parent.removeClass('has-error').addClass('has-success');
								}
								pjQ.$(label).remove();
							},
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionCheckout"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										hashBang("#!/Preview");
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
										self.enableButtons.call(self);
									}
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				}
				
			}).fail(function () {
				self.enableButtons.call(self);
			});
		},
		previewOrder: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionPreview"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				
				if (validate) {
					self.$container.find(".scSelectorPreviewForm").validate({
						rules: {},
						onkeyup: false,
						onclick: false,
						onfocusout: false,
						errorClass: "scError",
						validClass: "scValid",
						submitHandler: function (form) {
							self.disableButtons.call(self);
							pjQ.$('.scRefreshIcon').css('display', 'inline-block');
							var $form = pjQ.$(form);
							pjQ.$.post([self.options.folder, "index.php?controller=pjFrontCart&action=pjActionProcessOrder"].join(""), $form.serialize()).done(function (data) {
								if (data.status == "OK") {
									self.getPaymentForm.call(self, data);
								} else if (data.status == "ERR") {
									if(self.options.layout != '3')
									{
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
										pjQ.$('.scRefreshIcon').css('display', 'none');
									}else{
										$form
										.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("alert-success")
											.addClass("alert-warning")
											.show();
									}
									
									self.enableButtons.call(self);
								}
							}).fail(function () {
								self.enableButtons.call(self);
								pjQ.$('.scRefreshIcon').css('display', 'none');
							});
							return false;
						}
					});
				}
			});
		},
		getPaymentForm: function (obj) {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontCart&action=pjActionGetPaymentForm"].join(""), {
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme,
			    "order_id": obj.order_id,
			    "invoice_id": obj.invoice_id,
			    "payment_method": obj.payment_method
			}).done(function (data) {
				self.$container.html(data);
				switch (obj.payment_method) {
				case 'paypal':
					self.$container.find("form[name='scPaypal']").trigger('submit');
					break;
				case 'authorize':
					self.$container.find("form[name='scAuthorize']").trigger('submit');
					break;
				case 'creditcard':
				case 'bank':
				case 'cod':
					break;
				}
			}).fail(function () {
				log("Deferred is rejected");
			});
		},
		priceStock: function () {
			var m, $el, qs, i, iCnt, j, productObj = {}, $qty,
				$thumb, src, href,
				$form = this.$container.find(".scSelectorProductForm"),
				attr = $form.find(".scSelectorAttr").serializeArray();
			
			this.canBuy = true;
			for (i = 0, iCnt = attr.length; i < iCnt; i++) {
				m = attr[i].name.match(/attr\[(\d+)\]/);
				productObj[m[1]] = attr[i].value; 
				if(attr[i].value == '')
				{
					this.canBuy = false;
				}
			}

			for (i = 0, iCnt = this.stockObj.length; i < iCnt; i++) {
				if (ShoppingCart.compare(this.stockObj[i], productObj)) {
					this.priceStocks = parseFloat(this.priceObj[i]);
					this.unitPrice = this.priceStocks;
					// Change pic
					$thumb = this.$container.find(".scSelectorStockThumb[data-stock_id='" + this.stockIds[i] + "']");
					if ($thumb.length > 0) {
						src = $thumb.data("src");
						href = $thumb.data("large");
						if (src !== undefined && src.length > 0) {
							this.$container.find(".scSelectorProductPic").attr("src", src).parent("a.scSelectorFancy").attr("href", href);
						}
					}
					// Set qty attrs
					$qty = this.$container.find(":input[name='qty']");
					switch ($qty.get(0).nodeName) {
					case 'INPUT':
						$qty.val(1)
							.data("max", this.qtyObj[i])
							.attr("data-max", this.qtyObj[i])
							.attr("maxlength", this.qtyObj[i].length);
						break;
					case 'SELECT':
						$qty.empty();
						for (j = 1; j <= this.qtyObj[i]; j++) {
							pjQ.$("<option>")
								.attr("value", j)
								.text(j)
								.appendTo($qty);
						}
						break;
					}
					break;
				}
			}
			// Apply only if product have not attributes
			if (this.stockObj.length === 0 && this.priceObj[0]) {
				this.priceStocks = parseFloat(this.priceObj[0]);
				this.unitPrice = this.priceStocks; 
			}
			
			if(this.canBuy == true)
			{
				this.setPrice.call(this).showPrice.call(this);
			}else{
				this.$container.find(".scSelectorPrice").html(pjQ.$('.scHiddenMinPrice').html());
			}
		},
		priceExtra: function () {
			var $ele, $selected,
				price = 0;
			this.$container.find(".scSelectorExtra").each(function (i, ele) {
				$ele = pjQ.$(ele);
				switch (ele.nodeName) {
					case 'INPUT':
						if ($ele.is(":checked")) {
							price += parseFloat($ele.data("price"));
						}
						break;
					case 'SELECT':
						$selected = pjQ.$("option:selected", $ele);
						if ($selected) {
							price += parseFloat($selected.data("price"));
						}
						break;
				}
			});
			this.priceExtras = price;			
			if(this.canBuy == true)
			{
				this.setPrice.call(this).showPrice.call(this);
			}
		},
		formatCurrencySign: function(price)
		{
			var self = this,
				format = '---';
			
			switch (self.options.currency)
			{
				case 'USD':
					format = self.options.currencysign + price;
					break;
				case 'GBP':
					format = self.options.currencysign + price;
					break;
				case 'EUR':
					format = self.options.currencysign + price;
					break;
				case 'JPY':
					format = self.options.currencysign + price;
					break;
				case 'AUD':
				case 'CAD':
				case 'NZD':
				case 'CHF':
				case 'HKD':
				case 'SGD':
				case 'SEK':
				case 'DKK':
				case 'PLN':
					format = price + self.options.currencysign;
					break;
				case 'NOK':
				case 'HUF':
				case 'CZK':
				case 'ILS':
				case 'MXN':
					format = self.options.currencysign + price;
					break;
				default:
					format = price + self.options.currencysign;
					break;
			}
			return format;
		},
		setPrice: function () {
			this.price = parseFloat(this.priceStocks + (this.priceExtras * this.qty)).toFixed(2);
			//this.price = parseFloat(this.priceStocks + this.priceExtras).toFixed(2);
			return this;
		},
		showPrice: function () {
			this.$container.find(".scSelectorPrice").html(this.formatCurrencySign(this.price)).parent().show();
			return this;
		},
		changeQty: function (el, callback) {
			var self = this,
				$this = pjQ.$(el),
				$qty = $this.siblings(".scSelectorSpinValue"),
				current = parseInt($qty.val(), 10),
				min = parseInt($qty.data("min"), 10),
				max = parseInt($qty.data("max"), 10),
				direction = $this.data("direction"),
				qty = 1;
			switch (direction) {
				case "up":
					if (current + 1 <= max) {
						qty = current + 1;
						$qty.val(qty);
						pjQ.$('.scMaximumItems').html("").hide();
					}else{
						qty = max;
						var max_text = pjQ.$('.scMaximumItems').attr('data-text');
						max_text = max_text.replace("{MAX}", max); 
						pjQ.$('.scMaximumItems').html(max_text).show();
					}
					break;
				case "down":
					if (current - 1 >= min) {
						qty = current - 1;
						$qty.val(qty);
						pjQ.$('.scMaximumItems').html("").hide();
					}
					break;
			}
			self.qty = qty;
			self.priceStocks = qty * self.unitPrice;
			self.setPrice.call(self).showPrice.call(self);
			if (callback !== undefined) {
				callback();
			}
		},
		init: function (opts) {
			var self = this;
			this.options = opts;
			this.container = document.getElementById("scContainer_" + this.options.index);
			this.$container = pjQ.$(this.container);
			if(self.options.cid > 0)
			{
				self.category_id = self.options.cid;
			}
			this.$container.on("click.sc", ".scSelectorLocale", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var locale = pjQ.$(this).data("id");
				self.options.locale = locale;
				pjQ.$(this).addClass("scLocaleFocus").parent().parent().find("a.scSelectorLocale").not(this).removeClass("scLocaleFocus");
				
				pjQ.$.get([self.options.folder, "index.php?controller=pjFront&action=pjActionLocale"].join(""), {
					"locale_id": locale
				}).done(function (data) {
					if (hashBang("#!/Products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1))) {
					} else {
						self.loadProducts.call(self);
					}
				}).fail(function () {
					log("Deferred is rejected");
				});
				return false;
			}).on("mouseenter.sc", ".scSelectorProductItem", function () {
				pjQ.$(this).addClass("scProductItemHover");
			}).on("mouseleave.sc", ".scSelectorProductItem", function () {
				pjQ.$(this).removeClass("scProductItemHover");
			}).on("click.sc", ".scSelectorProduct", function (e) {
				if (e && e.precentDefault) {
					e.precentDefault();
				}
				var $this = pjQ.$(this),
					product_id = $this.data("id"),
					slug = $this.data("slug");
				if (self.options.seoUrl === 1 && slug.length > 0) {
					hashBang("#!/" + slug);
				} else {
					hashBang("#!/Product/" + product_id);
				}
				return false;
			}).on("click.sc", ".scSelectorProducts", function (e) {
				if (e && e.precentDefault) {
					e.precentDefault();
				}
				hashBang("#!/Products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
				return false;
			}).on("click.sc", ".scSelectorPage", function (e) {
				if (e && e.precentDefault) {
					e.precentDefault();
				}
				var page = pjQ.$(this).data("page");
				hashBang("#!/Products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (page && page !== undefined ? page : 1));
				return false;
			}).on("change.sc", ".scSelectorCategoryId", function (e) {
				var category_id = pjQ.$("option:selected", this).val();
				hashBang("#!/Products/q:/category:" + category_id + "/page:1");
			}).on("change.sc", ".scSelectorExtra", function (e) {
				self.priceExtra.call(self);
			}).on("change.sc", ".scSelectorAttr", function (e) {
				self.loopAttr.call(self, this);
				self.priceStock.call(self);
				self.checkFavs.call(self);
			}).on("click.sc", ".scSelectorSpin", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (pjQ.$(this).hasClass("scCallbackUpdate")) {
					self.changeQty.call(self, this, function () {
						self.updateCart.call(self);
					});
				} else {
					self.changeQty.call(self, this);
				}
				return false;
			}).on("change.sc", ".scSelectorQty", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (pjQ.$(this).hasClass("scCallbackUpdate")) {
					self.updateCart.call(self);
				}
				return false;
			}).on("click.sc", ".scSelectorAdd2Cart", function (e) {
				var $form = self.$container.find(".scSelectorProductForm");
				if($form.valid())
				{
					var qs = self.buildQueryString.call(self);
					if (!qs) {
						log("Stock Id not set");
						return;
					}
					self.addToCart.call(self, qs);
				}
			}).on("submit.sc", ".scSelectorBuyNowForm", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.addToCart.call(self, pjQ.$(this).serialize());
				return false;
			}).on("submit.sc", ".scSelectorProductForm", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				return false;
			}).on("click.sc", ".scSelectorProductThumb", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this),
					src = $this.data("src"),
					href = $this.data("large");
				if (src !== undefined && src.length > 0) {
					self.$container.find(".scSelectorProductPic").attr("src", src).parent("a.scSelectorFancy").attr("href", href);
				}
				return false;
			}).on("click.sc", ".scSelectorFancy", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $this = pjQ.$(this),
					href = $this.attr("href");
				self.$container.find("a[rel='fancy_group'][href='" +href+ "']").trigger("click");
				return false;
			}).on("click.sc", ".scSelectorAdd2Favs", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.addToFavs.call(self);
				return false;
			}).on("click.sc", ".scSelectorSend2Friend", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.$container.find(".scSelectorSend2FriendBox").toggle();
				return false;
			}).on("click.sc", ".scSelectorSend2FriendCancel", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.$container.find(".scSelectorSend2FriendBox").hide();
				return false;
			}).on("click.sc", ".scSelectorViewFavs", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Favs");
				return false;
			}).on("click.sc", ".scSelectorViewCart", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Cart");
				return false;
			
			}).on("change.sc", ".scSelectorOriginalB", function (e) {
				if (self.$container.find(".scSelectorAddressId").length > 0) {
					pjQ.$(window).trigger("compareAddress", {
						"type": "billing"
					});
				}
			}).on("change.sc", ".scSelectorOriginalS", function (e) {
				if (self.$container.find(".scSelectorAddressId").length > 0) {
					pjQ.$(window).trigger("compareAddress", {
						"type": "shipping"
					});
				}
			}).on("mouseenter.sc", ".scSelectorRemoveFromFavs, .scSelectorRemoveFromCart, .scSelectorEyeProduct", function (e) {
				var $img = pjQ.$(this).children("img");
				if ($img.length > 0) {
					$img.attr("src", $img.data("src"));
				}
			}).on("mouseleave.sc", ".scSelectorRemoveFromFavs, .scSelectorRemoveFromCart, .scSelectorEyeProduct", function (e) {
				var $img = pjQ.$(this).children("img");
				if ($img.length > 0) {
					$img.attr("src", $img.data("original"));
				}
				
			// Favs
			}).on("click.ac", ".scSelectorRemoveFromFavs", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.removeFromFavs.call(self, pjQ.$(this).data("hash"));
				return false;
			}).on("click.sc", ".scSelectorEmptyFavs", function (e) {
				self.emptyFavs.call(self);
			}).on("click.sc", ".scSelectorFav2Cart", function (e) {
				self.addToCart.call(self, pjQ.$(this).closest("form").serialize());
				
			// Cart (Basket)
			}).on("click.sc", ".scSelectorContinueShopping", function (e) {
				hashBang("#!/Products/q:" + (self.q && self.q !== undefined ? self.q : "") + "/category:" + (self.category_id && self.category_id !== undefined ? self.category_id : "") + "/page:" + (self.page && self.page !== undefined ? self.page : 1));
			}).on("click.sc", ".scSelectorEmptyCart", function (e) {
				self.emptyCart.call(self);
			}).on("click.sc", ".scSelectorUpdateCart", function (e) {
				self.updateCart.call(self);
			}).on("click.sc", ".scSelectorRemoveFromCart", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.removeFromCart.call(self, pjQ.$(this).data("hash"));
				return false;
			}).on("click.sc", ".scSelectorCheckout", function (e) {
				self.$container.find(".scSelectorCartForm").trigger("submit");
				//hashBang("#!/Checkout");
			}).on("click.sc", ".scSelectorTerms", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if (dialog) {
					var id = 'scSelectorTerms_' + self.options.index,
						$dialog = pjQ.$("#"+id),
						$window = pjQ.$(window),
				        width = $window.width() * 0.8,
				        height = $window.height() * 0.8;
					
					if ($dialog.length === 0) {
						$dialog = pjQ.$('<div id="'+ id +'"></div>');
						$dialog.dialog({
							modal: true,
							resizable: false,
							draggable: false,
							autoOpen: false,
							title: pjQ.$(this).data("title"),
							width: width,
							height: height,
							open: function () {
								$dialog.html(self.$container.find(".scSelectorTermsBody").html());
								$dialog.dialog("option", "position", "center");
							},
							buttons: {
								'OK': function () {
									$dialog.dialog("close");
								}
							}
						});
					}
					$dialog.dialog("open");
				}
				return false;
			}).on("click.sc", ".scSelectorRemoveCode", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.removeCode.call(self);
				return false;
			}).on("change.sc", ".scSelectorShipping", function () {
				self.updateCart.call(self);
			}).on("change.sc", ".scSelectorAddressId", function (e) {
				self.getAddress.call(self, this);
			}).on("change.sc", ".scSelectorSameAs", function () {
				if (pjQ.$(this).is(":checked")) {
					self.$container.find(".scSelectorBoxShipping").hide();
				} else {
					self.$container.find(".scSelectorBoxShipping").show();
				}
			}).on("change.sc", "select[name='payment_method']", function () {
				self.$container.find(".scCcWrap").hide();
				self.$container.find(".scBankWrap").hide();
				switch (pjQ.$("option:selected", this).val()) {
				case 'creditcard':
					self.$container.find(".scCcWrap").show();
					break;
				case 'bank':
					self.$container.find(".scBankWrap").show();
					break;
				}
			}).on("click.sc", ".scSelectorEditOrder", function () {
				hashBang("#!/Checkout");
				
			// Front Accounts
			}).on("click.sc", ".scSelectorLogin", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Login");
				return false;
			}).on("click.sc", ".scSelectorLogout", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.getLogout.call(self);
				return false;
			}).on("click.sc", ".scSelectorProfile", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Profile");
				return false;
			}).on("click.sc", ".scSelectorRegister", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Register");
				return false;
			}).on("click.sc", ".scSelectorForgot", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Forgot");
				return false;
			}).on("click.sc", ".scSelectorAddAddress", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $clone = self.$container.find(".scSelectorCloneAddress").eq(0).clone();
				self.$container.find(".scSelectorAddresses")/*.parent()*/.append($clone.html().replace(/\{INDEX\}/g, 'new_' + Math.ceil(Math.random() * 99999)));
				return false;
			}).on("click.sc", ".scSelectorRemoveAddress, .scSelectorDeleteAddress", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if(self.options.layout != '3')
				{
					pjQ.$(this).parent().parent().remove();
				}else{
					pjQ.$(this).parent().parent().parent().parent().remove();
				}
				return false;
				
			// Menu	
			}).on("mouseover.sc", ".scMenuBarItem", function (e) {
				pjQ.$(this).addClass("scMenuBarItemHover");
			}).on("mouseout.sc", ".scMenuBarItem", function (e) {
				pjQ.$(this).removeClass("scMenuBarItemHover");
			}).on("mouseover.sc", ".scMenuItem", function (e) {
				pjQ.$(this).addClass("scMenuItemHover");
			}).on("mouseout.sc", ".scMenuItem", function (e) {
				pjQ.$(this).removeClass("scMenuItemHover");
			}).on("click.sc", ".scMenuBar a, .scCartMenu a", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var href = pjQ.$(this).attr("href");
				hashBang(href.substring(href.indexOf("#")));
				return false;
				
			}).on("submit.sc", ".scSelectorSearchForm", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Products/q:" + encodeURIComponent( pjQ.$(this).find("input[name='q']").val() ) + "/category:/page:1");
				return false;
			}).on("click.sc", ".scGoBack", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Products");
			}).on("click.sc", ".scDropDownMenu", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) 
				{
				 
				}else{
					window.location.href = pjQ.$(this).attr('data-href');
				}
			}).on("click.sc", ".scStoreName", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				hashBang("#!/Products");
			}).on("click.sc", ".pjScEyeIcon", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				var $prevElement = pjQ.$(this).prev();
				if($prevElement.attr('type') == 'password')
				{
					$prevElement.attr('type', 'text');
					pjQ.$(this).attr('title', pjQ.$(this).attr('data-hide'));
					pjQ.$(this).addClass('active');
				}else{
					$prevElement.attr('type', 'password');
					pjQ.$(this).attr('title', pjQ.$(this).attr('data-show'));
					pjQ.$(this).removeClass('active');
				}
			}).on("click.sc", ".pjScBtnRemoveCode", function (e) {
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				self.disableButtons.call(self);
				
				pjQ.$.get([self.options.folder, "index.php?controller=pjFrontCart&action=pjActionRemoveCode"].join("")).done(function (data) {
					if (data.status == "OK") {
						self.viewCart.call(self);
					}
				}).fail(function () {
					self.enableButtons.call(self);
				});
			});
			
			//Custom events
			pjQ.$(window).on("loadCart", this.container, function (e) {
				self.viewCart.call(self);
			}).on("loadFavs", this.container, function (e) {
				self.viewFavs.call(self);
			}).on("loadProduct", this.container, function (e, product_id) {
				self.product_id = product_id;
				self.loadProduct.call(self);
			}).on("loadProducts", this.container, function (e, q, category_id, page) {
				self.q = q;
				self.category_id = category_id;
				self.page = page;
				self.loadProducts.call(self);
			}).on("loadLogin", this.container, function (e) {
				self.getLogin.call(self);
			}).on("loadForgot", this.container, function (e) {
				self.getForgot.call(self);
			}).on("loadProfile", this.container, function (e) {
				self.getProfile.call(self);
			}).on("loadRegister", this.container, function (e) {
				self.getRegister.call(self);
			}).on("loadCheckout", this.container, function (e) {
				self.checkoutCart.call(self);
			}).on("loadPreview", this.container, function (e) {
				self.previewOrder.call(self);
				
			}).on("reloadAddress", this.container, function (e, data) {
				switch (data.type) {
				case "billing":
					self.bData = {};
					self.$container.find(".scSelectorOriginalB").each(function (i, el) {
						self.bData[this.getAttribute("name")] = this.getAttribute("data-original");
					});
					break;
				case "shipping":
					self.sData = {};
					self.$container.find(".scSelectorOriginalS").each(function (i, el) {
						self.sData[this.getAttribute("name")] = this.getAttribute("data-original");
					});
					break;
				}
			}).on("compareAddress", this.container, function (e, data) {
				var tmp = {};
				switch (data.type) {
				case "billing":
					self.$container.find(".scSelectorOriginalB").each(function (i, el) {
						tmp[this.getAttribute("name")] = this.nodeName !== "SELECT" ? this.value : this.options[this.selectedIndex].value;
					});
					if (ShoppingCart.compare(self.bData, tmp)) {
						self.$container.find(".scSelectorSaveB").hide().find("input[name='b_save']").removeAttr("checked");
					} else {
						self.$container.find(".scSelectorSaveB").show();
					}
					break;
				case "shipping":
					self.$container.find(".scSelectorOriginalS").each(function (i, el) {
						tmp[this.getAttribute("name")] = this.nodeName !== "SELECT" ? this.value : this.options[this.selectedIndex].value;
					});
					if (ShoppingCart.compare(self.sData, tmp)) {
						self.$container.find(".scSelectorSaveS").hide().find("input[name='s_save']").removeAttr("checked");
					} else {
						self.$container.find(".scSelectorSaveS").show();
					}
					break;
				}
			});
			
			if (window.location.hash.length === 0) {
				this.loadProducts.call(this);
			} else {
				onHashChange.call(null);
			}
			
			return this;
		},
		loadProducts: function () {
			var self = this;
			this.resetProduct.call(this);
			pjQ.$.get([this.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProducts"].join(""), {
				"q": this.q,
				"category_id": this.category_id,
				"page": this.page,
				"locale": this.options.locale,
				"hide": this.options.hide,
				"layout": this.options.layout,
				"theme": this.options.theme
			}).done(function (data) {
				self.$container.html(data);
				if(self.page != null)
				{
					pjQ.$("html, body").animate({ scrollTop: 0 }, "slow");
				}
				pjQ.$('.scDropDownMenu').bind('touchstart', function(e){
					e.preventDefault();
					var $this = pjQ.$(this);
					if ($this.siblings().size() > 0) 
					{
						if(!$this.siblings(':first').hasClass('scShown'))
						{
							$this.siblings(':first').addClass('scShown');
							$this.siblings(':first').css('display', 'block');
						}else{
							window.location.href = $this.attr('data-href');
						}
						
					}else{
						window.location.href = $this.attr('data-href');
					}
					return false;
				});
			});
		},
		loadProduct: function () {
			var self = this;
			pjQ.$.get([this.options.folder, "index.php?controller=pjFront&action=pjActionGetStocks&id=", this.product_id].join("")).done(function (data) {
				self.stockIds = data.stock_ids;
				self.stockObj = data.stocks;
				self.qtyObj = data.qty;
				self.priceObj = data.price;
				self.attrObj = data.attributes;
				pjQ.$.get([self.options.folder, "index.php?controller=pjFrontPublic&action=pjActionProduct&id=", self.product_id].join(""), {
					"locale": self.options.locale,
					"hide": self.options.hide,
					"layout": self.options.layout,
					"theme": self.options.theme
				}).done(function (data) {
					self.$container.html(data);
					self.loopAttr.call(self, self.$container.find(".scSelectorAttr:first").get(0));
					self.priceStocks = parseFloat(pjQ.$('.scInputMinPrice').val());
					self.unitPrice = self.priceStocks;
					self.checkFavs.call(self);
					if (fancybox) {
						self.$container.find("a[rel=fancy_group]").fancybox();
					}
					
					if (validate) {
						var $form = self.$container.find(".scSelectorProductForm");
						$form.validate({
							onkeyup: false,
							highlight: function(ele, errorClass, validClass) {
								pjQ.$(ele).parent().addClass('has-error');
				            },
				            unhighlight: function(ele, errorClass, validClass) {
				            	pjQ.$(ele).parent().removeClass('has-error').addClass('has-success');
				            }
						});
						var $form = self.$container.find(".scSelectorSend2FriendForm");
						$form.validate({
							rules: {
								"your_email": {
									required: true,
									email: true
								},
								"your_name": "required",
								"friend_email": {
									required: true,
									email: true
								},
								"friend_name": "required",
								"captcha": {
									required: true,
									minlength: 6,
									maxlength: 6,
									remote: self.options.folder + "index.php?controller=pjFront&action=pjActionCheckCaptcha"
								}
							},
							messages: {
								"your_email": {
									required: $form.find("input[name='your_email']").attr('data-err'),
									email: $form.find("input[name='your_email']").attr('data-email')
								},
								"your_name": $form.find("input[name='your_name']").attr('data-err'),
								"friend_email": {
									required: $form.find("input[name='friend_email']").attr('data-err'),
									email: $form.find("input[name='friend_email']").attr('data-email')
								},
								"friend_name": $form.find("input[name='friend_name']").attr('data-err'),
								"captcha": {
									required: $form.find("input[name='captcha']").attr('data-err'),
									remote: $form.find("input[name='captcha']").attr('data-captcha')
								}
							},
							onkeyup: false,
							onclick: false,
							onfocusout: false,
							errorClass: "scError",
							validClass: "scValid",
							submitHandler: function (form) {
								self.disableButtons.call(self);
								var $form = pjQ.$(form);
								pjQ.$.post([self.options.folder, "index.php?controller=pjFront&action=pjActionSendToFriend"].join(""), $form.serialize()).done(function (data) {
									if (data.status == "OK") {
										$form.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeError")
											.addClass("scNoticeSuccess")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
										$form.find(":input").not(":button, :submit, :reset, :hidden").val("").removeAttr("checked").removeAttr("selected");
										var $captcha = $form.find(".scSelectorCaptcha").eq(0);
										$captcha.attr("src", $captcha.attr("src").replace(/(&rand=)\d+/g, '\$1' + Math.ceil(Math.random() * 99999)));
									} else if (data.status == "ERR") {
										$form
											.find(".scSelectorNoticeMsg")
											.html(data.text)
											.removeClass("scNoticeSuccess")
											.addClass("scNoticeError")
											.prepend( pjQ.$("<div>").addClass("scNoticeIcon") )
											.show();
									}
									self.enableButtons.call(self);
								}).fail(function () {
									self.enableButtons.call(self);
								});
								return false;
							}
						});
					}
				});
			});
		},
		loopAttr: function (el) {
			var oid, valid, k, kCnt, j, jCnt, b, bCnt, pid, $select, $option,
				self = this,
				$el = pjQ.$(el),
				row = $el.data("row"),
				id = $el.find("option:selected").val(),
				stocks = [];

			for (k = 0, kCnt = self.stockObj.length; k < kCnt; k++) {
				if (ShoppingCart.inObject(id, this.stockObj[k])) {
					stocks.push(this.stockObj[k]);
				}
			}

			this.$container.find(".scSelectorAttr").each(function (i, select) {
				if (i > row) {
					$select = pjQ.$(select);
					$select.empty();
					pid = $select.data("id");
					pjQ.$("<option>")
						.attr("value", "")
						.text($select.attr('data-choose'))
						.appendTo($select);
					for (k = 0, kCnt = self.attrObj.length; k < kCnt; k++) {
						if (self.attrObj[k].id != pid) {
							continue;
						}
						for (j = 0, jCnt = self.attrObj[k].child.length; j < jCnt; j++) {
							for (b = 0, bCnt = stocks.length; b < bCnt; b++) {
								if (ShoppingCart.inObject(self.attrObj[k].child[j].id, stocks[b]) || (stocks[b][pid] && stocks[b][pid] == 0)) {
									pjQ.$("<option>")
										.attr("value", self.attrObj[k].child[j].id)
										.text(self.attrObj[k].child[j].name)
										.appendTo($select);
									break;
								}
							}
						}
					}
				}
			});
		},
		resetProduct: function () {
			this.stockIds = {};
			this.stockObj = {};
			this.qtyObj = {};
			this.priceObj = {};
			this.attrObj = {};
			this.price = 0.00;
			this.priceStocks = 0;
			this.priceExtras = 0
			return this;
		}
	};
	
	// expose
	window.ShoppingCart = ShoppingCart;	
})(window);