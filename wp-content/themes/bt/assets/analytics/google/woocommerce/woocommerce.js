($ => {
	const displayConsoleLog = true;
	
	const productsLoopSelector 				  = 'ul.products';
	const productLoopSelector 				  = 'li.product';
	const productsProductLoopSelector 		  = productsLoopSelector + ' ' + productLoopSelector;
	const paginationSelector 				  = '.woocommerce-pagination';
	const paginationPrevSelector 			  = paginationSelector + ' a.prev';
	const paginationNextSelector 			  = paginationSelector + ' a.next';
	const classCatPrefix 					  = 'product_cat-';
	const classProductIdPrefix 				  = 'post-';
	const classProductIdRegex 				  = '^' + classProductIdPrefix + '[0-9]+$';
	const productBrandExists 				  = false;
	const defaultBrandName 					  = 'bt';
	const productBrandSlug 					  = '';
	const classBrandPrefix 					  = 'product_' + productBrandSlug + '-'
	const singleOrPriceRangeSelector 		  = '.price > .woocommerce-Price-amount > bdi'; 
	const salePriceSelector 				  = '.price > ins > .woocommerce-Price-amount > bdi';
	const variationSingleOrPriceRangeSelector = '.woocommerce-variation-price ' + singleOrPriceRangeSelector;
	const variationSalePriceSelector 		  = '.woocommerce-variation-price ' + salePriceSelector;
	const productLoopNameSelector 			  = '.woocommerce-loop-product__title';
	const productLoopAddToCart 				  = productsProductLoopSelector + ' a[href^="?add-to-cart"]';
	const productLoopView 					  = productsProductLoopSelector + ' a[href^="http"]';
	const productSingleSelector 			  = '.single-product [id^="product-"]';
	const productSingleSummarySelector 		  = '.summary.entry-summary';
	const productSingleAddToCart 			  = '.cart [type="submit"]:not([name="update_cart"]):not(.disabled)';
// 	const productSingleAddToCart 			  = '.cart [type="submit"]';
	const productSingleNameSelector 		  = '.product_title.entry-title';
	const relatedProductsSelector 			  = '.related.products';
	const productVariationsSelector 		  = '.variations select';
	const cartPageSelector 					  = '.woocommerce-cart';
	const cartProductsSelector 				  = cartPageSelector + ' .woocommerce-cart-form';
	const cartProductSelector 				  = cartProductsSelector + ' > table tbody tr[class]';
	const cartProductPriceSelector			  = '.product-price > span > bdi';
	const cartProductNameSelector			  = '.product-name';
	const cartProductQuantitySelector 		  = '.product-quantity [type="number"]';
	const cartRemoveProductSelector 		  = '.product-remove .remove';
	const cartUpdateCartSelector 		  	  = '[name="update_cart"]';
	const checkoutPageSelector 				  = '.woocommerce-checkout:not(.woocommerce-order-received)';
	const checkoutProductsSelector 			  = checkoutPageSelector + ' .woocommerce-checkout-review-order .woocommerce-checkout-review-order-table';
	const checkoutProductSelector 			  = checkoutProductsSelector + ' tbody tr[class]';
	const checkoutProductPriceSelector 		  = '.product-total > span > bdi';
	const checkoutProductNameSelector 		  = '.product-name';
	const checkoutProductQuantitySelector 	  = checkoutProductNameSelector + ' .product-quantity';
	const checkoutShippingOptionsSelector 	  = checkoutPageSelector + ' .woocommerce-shipping-totals.shipping';
	const thankyouPageSelector 				  = '.woocommerce-order-received';
	const thankyouProductsSelector 			  = thankyouPageSelector + ' .woocommerce-order-details';
	const thankyouProductSelector 			  = thankyouProductsSelector + ' tbody tr[class]';
	const thankyouPaymentMethod 			  = '.woocommerce-order-overview__payment-method strong';
	const thankyouProductPriceSelector 		  = '.woocommerce-table__product-total > span > bdi';
	const thankyouProductNameSelector 		  = '.woocommerce-table__product-name a';
	const thankyouProductQuantitySelector 	  = '.woocommerce-table__product-name .product-quantity';
	const tankyouOrderTotalSelector 		  = '.woocommerce-order-overview__total > strong > span > bdi';
	const tankyouOrderIdSelector		  	  = '.woocommerce-order-overview__order strong';
	const tankyouOrderDetailsFooterSelector   = thankyouProductsSelector + ' tfoot tr';

	$(document).on('click', cartUpdateCartSelector, () => {
		// all products for remove from cart
		let products = [];
		
		$(cartProductSelector).map((i, e) => {
			const $this = $(e);

			const product = prepareProductImpressionObj($this, i);

			// remove item list name
			delete product.item_list_name;

			// remove index (product position)
			delete product.index;

			// set product quantity
			product.quantity = $this.find(cartProductQuantitySelector).val();

			if (product.quantity == 0) {
				product.quantity = $this.find(cartProductQuantitySelector).attr('value');
				products.push(product);
			}
		});

		// remove from cart object that will be pushed into dataLayer
		if (products.length === 0) return;

		const removeFromCart = {
			event: 'remove_from_cart',
			ecommerce: {
				items: products
			}
		};

		if (displayConsoleLog) console.log(removeFromCart);

		// push remove from cart
		dataLayer.push(removeFromCart);
	});
	
	// push remove from cart to dataLayer
	$(document).on('click', cartProductsSelector + ' ' + cartRemoveProductSelector, e => {
		const $this = $(e.currentTarget).closest('tr');

		const product = prepareProductImpressionObj($this, 0);

		// remove item list name
		delete product.item_list_name;

		// remove index (product position)
		delete product.index;

		// set product quantity
		product.quantity = $this.find(cartProductQuantitySelector).val();

		// remove from cart object that will be pushed into dataLayer
		const removeFromCart = {
			event: 'remove_from_cart',
			ecommerce: {
				items: [product]
			}
		};

		if (displayConsoleLog) console.log(removeFromCart);
		
		// push remove from cart
		dataLayer.push(removeFromCart);
	});
	
	// push product add to cart in single to dataLayer
	$(document).on('click', productSingleAddToCart, (e) => {
		const $this = $(e.currentTarget);

		// find the product (parent)
		const $product = $this.closest('[id^="product-"]');
		
		// prepare the product object
		let product = prepareProductImpressionObj($product, 0);
		
		// set product variations
		const variations = prepareProductVariations($product);
		
		// combine variation with product
		product = {
			...product,
			...variations
		};
		
		// set product quantity
		product.quantity = $('[name="quantity"]').val();
		
		// remove item list name, no relevant in add to cart
		delete product.item_list_name;
		
		// product add to cart object that will be pushed into dataLayer
		const addToCart = {
			event: 'add_to_cart',
			ecommerce: {
				items: [product]
			}
		};
		
		if (displayConsoleLog) console.log(addToCart);
		
		// push add to cart
		dataLayer.push(addToCart);
	});
	
	// push product view in loop to dataLayer
	$(productLoopView).on('click', (e) => {
		const $this = $(e.currentTarget);

		// find the product (parent)
		const $product = $this.closest('.product');

		// prepare the product object
		let product = prepareProductImpressionObj($product, $product.index());

		// set quantity
		product.quantity = 1;
		
		// product view object that will be pushed into dataLayer
		const productView = {
			event: 'select_item',
			ecommerce: {
				items: [product]
			}
		};
		
		if (displayConsoleLog) console.log(productView);
		
		// push product view
		dataLayer.push(productView);
	});

	// push add to cart in loop to dataLayer
	$(productLoopAddToCart).on('click', (e) => {
		const $this = $(e.currentTarget);
		
		// find the product (parent)
		const $product = $this.closest('.product');
		
		// prepare the product object
		let product = prepareProductImpressionObj($product, $product.index());
		
		// set quantity
		product.quantity = 1;
		
		// remove item list name, no relevant in add to cart
		delete product.item_list_name;
		
		// add to cart object that will be pushed into dataLayer
		const addToCart = {
			event: 'add_to_cart',
			ecommerce: {
				items: [product]
			}
		}
		
		if (displayConsoleLog) console.log(addToCart);
		
		// push add to cart
		dataLayer.push(addToCart);
	});
	
	// set product variations
	function prepareProductVariations ($product) {
		// product variations
		let variations = {};
		
		// check the type of product, basically if in product/cart/checkout
		if ($product.is('[id^="product-"]')) {
			$(productVariationsSelector).map((i, e) => {
				const $this = $(e);
				
				// get variation (select) name
				const variationName = $this.attr('name');

				// based on name prefix get only the attribute name
				if (variationName.startsWith('attribute_pa_')) {
					variations['item_variation_' + variationName.substring('attribute_pa_'.length)] = $this.val();
				} else if (variationName.startsWith('attribute_')) {
					variations['item_variation_' + variationName.substring('attribute_'.length)] = $this.val();
				}
			});
		}
		
		return variations;
	}

	// check if in checkout and have products in cart
	function checkoutProductsExists () {
		if ($(checkoutProductsSelector).length === 0) return false;
		return true;
	}
	
	// check if in cart and have products in cart
	function cartProductsExists () {
		if ($(cartProductsSelector).length === 0) return false;
		return true;
	}
	
	// check if products loop exists
	function productsLoopExists () {
		if ($(productsProductLoopSelector).length === 0) return false;
		return true;
	}
	
	// check if thankyou page exists
	function thankyouPageExists () {
		if ($(thankyouPageSelector).length === 0) return false;
		return true;
	}
	
	// exit if no shipping options found
	function shippingOptionsExists () {
		if ($(checkoutShippingOptionsSelector).length === 0) return false;
		return true;
	}

	// check if single product exists
	function singleProductExists () {
		const singleProductLength = $(productSingleSelector).length;
		
		if (singleProductLength === 0 || singleProductLength > 1) return false;
		return true;
	}

	// push single product immpression into dataLayer
	function dataLayerPushSingleProductImpression () {
		// exit if not in single product
		if (!singleProductExists()) return;
		
		// get product element
		const $product = $(productSingleSelector);
		
		// prepare the product object
		let product = prepareProductImpressionObj($product, $product.index());
		
		// set quantity
		product.quantity = 1;
		
		// update index (product position)
		product.index = 0;
		
		// remove item list name, no relevant in single product impression
		delete product.item_list_name;
		
		// view item object that will be pushed into dataLayer
		const viewItem = {
			event: 'view_item',
			ecommerce: {
				items: [product]
			}
		}
		
		if (displayConsoleLog) console.log(viewItem);
		
		// push view item (product impression)
		dataLayer.push(viewItem);
	}
	
	// push cart products into dataLayer
	function dataLayerPushCartImpressions () {
		// exit if not in cart or cart is empty
		if (!cartProductsExists()) return;
		
		// all products for impressions
		let products = [];
		
		$(cartProductSelector).map((i, e) => {
			const $this = $(e);
			
			const product = prepareProductImpressionObj($this, i);

			// remove item list name
			delete product.item_list_name;

			// remove index (product position)
			delete product.index;
			
			// set product quantity
			product.quantity = $this.find(cartProductQuantitySelector).val();

			products.push(product);
		});
		
		// check if products were added
		if (products.length === 0) return;
		
		// view cart object that will be pushed into dataLayer
		const viewCart = {
			event: 'view_cart',
			ecommerce: {
				items: products
			}
		};
		
		if (displayConsoleLog) console.log(viewCart);
		
		// push view item (product impression)
		dataLayer.push(viewCart);
	}
	
	// push begin checkout into dataLayer
	function dataLayerPushBeginCheckout () {
		// exit if not in checkout or cart is empty
		if (!checkoutProductsExists()) return;
		
		// all products for impressions
		let products = [];

		$(checkoutProductSelector).map((i, e) => {
			const $this = $(e);

			const product = prepareProductImpressionObj($this, i);

			// remove item list name
			delete product.item_list_name;

			// remove index (product position)
			delete product.index;

			// set product quantity
			product.quantity = $this.find(checkoutProductQuantitySelector).text().match(/\d+/)[0];

			products.push(product);
		});

		// check if products were added
		if (products.length === 0) return;

		// begin checkout object that will be pushed into dataLayer
		const beginCheckout = {
			event: 'begin_checkout',
			ecommerce: {
				items: products
			}
		};

		if (displayConsoleLog) console.log(beginCheckout);

		// push begin checkout (products impression)
		dataLayer.push(beginCheckout);
	}
	
	// push shipping option into dataLayer
	function dataLayerPushShippingOption () {
		// exit if no shipping options found
		if (!shippingOptionsExists()) return;
		
		// all products for shipping option (why?!)
		let products = [];

		$(checkoutProductSelector).map((i, e) => {
			const $this = $(e);

			const product = prepareProductImpressionObj($this, i);

			// remove item list name
			delete product.item_list_name;

			// remove index (product position)
			delete product.index;

			// set product quantity
			product.quantity = $this.find(checkoutProductQuantitySelector).text().match(/\d+/)[0];

			products.push(product);
		});

		// check if products were added
		if (products.length === 0) return;
		
		// the label element of the selected shipping option
		const $checkedInputLabel = $(checkoutShippingOptionsSelector).find('[type="radio"]:checked').closest('li').find('label');
		
		// shipping option object that will be pushed into dataLayer
		const shippingOption = {
			event: 'add_shipping_info',
			ecommerce: {
				shipping_tier: $checkedInputLabel.text(),
				items: products
			}
		};
		
		if (displayConsoleLog) console.log(shippingOption);
		
		// push shipping info
		dataLayer.push(shippingOption);
	}
	
	// push payment info into dataLayer
	function dataLayerPushPaymentInfo () {
		// exit if not in thankyou page
		if (!thankyouPageExists()) return;
		
		// all products for payment info
		let products = [];

		$(thankyouProductSelector).map((i, e) => {
			const $this = $(e);

			const product = prepareProductImpressionObj($this, i);

			// remove item list name
			delete product.item_list_name;

			// remove index (product position)
			delete product.index;

			// set product quantity
			product.quantity = $this.find(thankyouProductQuantitySelector).text().match(/\d+/)[0];;

			products.push(product);
		});

		// check if products were added
		if (products.length === 0) return;

		// payment info object that will be pushed into dataLayer
		const paymentInfo = {
			event: 'add_payment_info',
			ecommerce: {
				payment_type: $(thankyouPaymentMethod).text(),
				items: products
			}
		};

		if (displayConsoleLog) console.log(paymentInfo);

		// push payment info
		dataLayer.push(paymentInfo);
	}
	
	// push purchase info into dataLayer
	function dataLayerPushPurchaseInfo () {
		// exit if not in thankyou page
		if (!thankyouPageExists()) return;

		// all products for purchase info
		let products = [];

		$(thankyouProductSelector).map((i, e) => {
			const $this = $(e);

			const product = prepareProductImpressionObj($this, i);

			// remove item list name
			delete product.item_list_name;

			// remove index (product position)
			delete product.index;

			// set product quantity
			product.quantity = $this.find(thankyouProductQuantitySelector).text().match(/\d+/)[0];;

			products.push(product);
		});

		// check if products were added
		if (products.length === 0) return;
		
		// purchase info object that will be pushed into dataLayer
		const purchaseInfo = {
			event: 'purchase',
			ecommerce: {
				currency: btWooGlobals.currencyCode,
				value: parseFloat($(tankyouOrderTotalSelector).clone().children().remove().end().text()),
				transaction_id: $(tankyouOrderIdSelector).text(),
				items: products
			}
		};
		
		// add footer details
		$(tankyouOrderDetailsFooterSelector).map((i, e) => {
			const $this = $(e);

			switch ($this.find('th').text()) {
				case 'משלוח:': if ($this.find('.woocommerce-Price-amount').length > 0) purchaseInfo.ecommerce.shipping = parseFloat($this.find('.woocommerce-Price-amount').clone().children().remove().end().text()); break;
				case 'סך הכל:': if ($this.find('.includes_tax').length > 0) purchaseInfo.ecommerce.tax = parseFloat($this.find('.includes_tax > span').clone().children().remove().end().text()); break;
			}
		});

		if (displayConsoleLog) console.log(purchaseInfo);

		// push purchase info
		dataLayer.push(purchaseInfo);
	}
	
	// push impressions into dataLayer
	function dataLayerPushLoopImpressions () {
		// exit if no products loop found
		if (!productsLoopExists()) return;
		
		// all products for impressions
		let products = [];
		
		$(productsProductLoopSelector).map((i, e) => {
			const $this = $(e);
			
			products.push(prepareProductImpressionObj($this, i));
		});
		
		// check if products were added
		if (products.length === 0) return;
		
		// impressions object that will be pushed into dataLayer
		const impressions = {
			event: 'view_item_list',
			ecommerce: {
				items: products
			}
		};
		
		if (displayConsoleLog) console.log(impressions);
		
		// push impressions
		dataLayer.push(impressions);
	}

	function prepareProductImpressionObj ($this, position) {
		// product object for impressions
		let product = {
			index: position,
			item_name: getProductName($this)
		};

		// set brand name
		if (productBrandExists && defaultBrandName) product.item_brand = defaultBrandName;
		
		// get all classes of product
		let classes = $this.is('[class]') ? $this.attr('class').split(/\s+/) : [];

		// create the product id regex
		const product_id_regex = new RegExp(classProductIdRegex);

		// the number of the found category
		let categoryNum = 0;
		
		// indicates if sku was found
		let foundSku = false;
		
		// find relevant classes and add them to the product object
		classes.forEach((className) => {
			if (className.startsWith('product_sku-')) {
				foundSku = true;
				product.item_id = className.substring('product_sku-'.length);
			}
			else if (className.startsWith('product_attribute_')) {
				const variation = getProductVariations(className);
				
				product = {
					...product,
					...variation
				};
			}
			else if (!foundSku && product_id_regex.test(className)) product.item_id = className.substring(classProductIdPrefix.length); // set product id
			else if (className.startsWith(classCatPrefix)) {
				categoryNum++;
				
				if (categoryNum === 1) product['item_category'] = className.substring(classCatPrefix.length); // set product category 
				else product['item_category_' + categoryNum] = className.substring(classCatPrefix.length); // set product category 
			}
			else if (productBrandExists && className.startsWith(classBrandPrefix)) product.item_brand = className.substring(classBrandPrefix.length); // set product brand
		});

		// set list name
		product.item_list_name = getListName($this);

		// set price
		product.price = getProductprice($this);
		
		return product;
	}

	function getProductVariations (className) {
		// product variations
		let variations = {};
		
		if (className.startsWith('product_attribute_pa_')) {
			// get attribute name and value
			var classNameParts = className.substring('product_attribute_pa_'.length).split('_');
		} else if (className.startsWith('product_attribute_')) {
			// get attribute name and value
			var classNameParts = className.substring('product_attribute_'.length).split('_');
		}
		
		// set variation object key
		const attributeKey = classNameParts[0];
		
		// set variation value
		classNameParts.shift();
		
		if (Array.isArray(classNameParts)) classNameParts = classNameParts.join('_');
		
		variations['item_variation_' + attributeKey] = classNameParts;
		
		return variations;
	}
	
	function getListName ($this) {
		// check if in related products and update list name accordingly
		if ($this.closest(relatedProductsSelector).length > 0) return 'Related products';
		
		// get body element
		const $body = $('body');
		
		// get all classes of body
		let classes = $body.attr('class').split(/\s+/);
		
		let foundClassName = '';
		
		// find relevant class name and return it
		classes.forEach((className) => {
			if (className.startsWith('woo-list-name_')) foundClassName = className.substring('woo-list-name_'.length);
		});
		
		return foundClassName;
	}
	
	function getProductName ($this) {
		// loop product name
		const $loopProductName = $this.find(productLoopNameSelector);
		
		// single product name
		const $singleProductName = $this.find(productSingleNameSelector);

		if ($this.closest(thankyouProductsSelector).length > 0) {
			const name = $this.find(thankyouProductNameSelector).clone().children().remove().end().text().trim();
			
			// check if name has variables
			if (!name.includes(' - ')) return name;
			
			let nameParts = name.split(' - ');
			
			// remove variables from name
			nameParts.pop();
			
			if (Array.isArray(nameParts)) nameParts = nameParts.join(' - ');
			
			return nameParts;
		} else if ($this.closest(checkoutProductsSelector).length > 0) {
			return $this.find(checkoutProductNameSelector).clone().children().remove().end().text().trim();
		} else if ($this.closest(cartProductsSelector).length > 0) {
			const name = $this.find(cartProductNameSelector).text().trim();
			
			// check if name has variables
			if (!name.includes(' - ')) return name;
			
			let nameParts = name.split(' - ');
			
			// remove variables from name
			nameParts.pop();
			
			if (Array.isArray(nameParts)) nameParts = nameParts.join(' - ');
			
			return nameParts;
		}
		else if ($singleProductName.length > 0) return $singleProductName.text().trim();
		else if ($loopProductName.length > 0) return $loopProductName.text().trim();
	}
	
	// handle product prices options and return valid price format
	function getProductprice ($this) {
		let singleProductSelectorPrefix = '';
		
		if ($this.is('[id^="product-"]')) singleProductSelectorPrefix = productSingleSummarySelector + ' ';
		
		// get single OR price range elements
		const $regularPrice = $this.find(singleProductSelectorPrefix + singleOrPriceRangeSelector);

		// get sale price element
		const $salePrice = $this.find(singleProductSelectorPrefix + salePriceSelector);
		
		// get variation price element
		const $variationRegularPrice = $this.find(singleProductSelectorPrefix + variationSingleOrPriceRangeSelector);
		
		// get variation price element
		const $variationSalePrice = $this.find(singleProductSelectorPrefix + variationSalePriceSelector);

		// find the type of price and set it
		if ($this.closest(thankyouProductsSelector).length > 0) {
			// get product quantity
			const quantity = $this.find(thankyouProductQuantitySelector).text().match(/\d+/)[0];

			return parseFloat($this.find(thankyouProductPriceSelector).last().clone().children().remove().end().text()) / quantity;
		} else if ($this.closest(checkoutProductsSelector).length > 0) {
			// get product quantity
			const quantity = $this.find(checkoutProductQuantitySelector).text().match(/\d+/)[0];

			return parseFloat($this.find(checkoutProductPriceSelector).last().clone().children().remove().end().text()) / quantity;
		}
		else if ($this.closest(cartProductsSelector).length > 0) return parseFloat($this.find(cartProductPriceSelector).last().clone().children().remove().end().text());
		else if ($variationSalePrice.length > 0) return parseFloat($variationSalePrice.last().clone().children().remove().end().text());
		else if ($variationRegularPrice.length > 0) return parseFloat($variationRegularPrice.clone().children().remove().end().text());
		else if ($regularPrice.length > 0) {
			switch ($regularPrice.length) {
				case 1:
					return parseFloat($regularPrice.clone().children().remove().end().text());
				case 2:
					return parseFloat($regularPrice.last().clone().children().remove().end().text());
					break;
			}
		} else if ($salePrice.length > 0) return parseFloat($salePrice.clone().children().remove().end().text());
	}
	
	dataLayerPushLoopImpressions();
	dataLayerPushSingleProductImpression();
	dataLayerPushCartImpressions();
	dataLayerPushBeginCheckout();
	dataLayerPushShippingOption();
	dataLayerPushPaymentInfo();
	dataLayerPushPurchaseInfo();
	
	$(document).ajaxComplete((event, xhr, settings) => {
		if (settings.url === '/?wc-ajax=get_refreshed_fragments') dataLayerPushCartImpressions();
	});
	
	// run shipping options data layer push if selected a new, unchecked, option
	$(document).on('click', checkoutShippingOptionsSelector + ' [type="radio"]:not([checked])', e => {
		dataLayerPushShippingOption();
	});
	
	//===============================================================
	// ADMIN
	//===============================================================

	const $adminBody   				 		= $('body.wp-admin');
	const $orderStatus 				 		= $('#order_status');
	const saveOrderSelector 		 		= '[type="submit"].save_order';
	const orderIdSelector 			 		= '[name="post_ID"]';
	const adminOrderProductsSelector 		= '.woocommerce_order_items_wrapper.wc-order-items-editable #order_line_items .item';
	const adminOrderProductQuantitySelector = '.quantity .view';
	const adminOrderProductNameSelector 	= '.wc-order-item-name';
	const adminOrderProductPriceSelector 	= '.item_cost .view > span > bdi';
	
	// check if in the correct location (single order edit)
	if ($adminBody.length > 0 && $orderStatus.length > 0) {
		// get current order status
		let currentOrderStatus = $orderStatus.val();
		
		// get all orders statuses
		let adminOrdersStatuses = localStorage.getItem('adminOrdersStatuses');
		
		// update current order status
		$orderStatus.on('change', () => {
			// get selected order status
			const selectedOrderStatus = $orderStatus.val();
			
			// check that both order statuses are not the same
			if (currentOrderStatus !== selectedOrderStatus) {
				// based on what orders statuses contain create the proper object
				if (adminOrdersStatuses === null) adminOrdersStatuses = {};
				else adminOrdersStatuses = JSON.parse(adminOrdersStatuses);
				
				// set/update current order status
				adminOrdersStatuses[$(orderIdSelector).val()] = currentOrderStatus;
				
				// set the new orders statuses json object
				localStorage.setItem('adminOrdersStatuses', JSON.stringify(adminOrdersStatuses));
			}
		});
		
		((adminOrdersStatuses, orderIdSelector) => {
			if (adminOrdersStatuses === null) return;
			else adminOrdersStatuses = JSON.parse(adminOrdersStatuses);
			
			const orderId = $(orderIdSelector).val()
			
			if (adminOrdersStatuses[orderId] === undefined || adminOrdersStatuses[orderId] === $orderStatus.val()) return;

			const orderStatus = $orderStatus.val();

			// all products for purchase info
			let products = [];

			$(adminOrderProductsSelector).map((i, e) => {
				const $this = $(e);

				const product = prepareProductImpressionObj($this, i);
				
				// remove item list name
				delete product.item_list_name;

				// remove index (product position)
				delete product.index;

				// set product quantity
				product.quantity = parseFloat($this.find(adminOrderProductQuantitySelector).clone().children().remove().end().text());

				// set product name
				const name = $this.find(adminOrderProductNameSelector).text().trim();

				// check if name has variables
				if (name.includes(' - ')) {
					let nameParts = name.split(' - ');

					// remove variables from name
					nameParts.pop();

					if (Array.isArray(nameParts)) nameParts = nameParts.join(' - ');

					product.item_name = nameParts;
				} else {
					product.item_name = name;
				}
				// set product name - end
				
				// set product price
				product.price = parseFloat($this.find(adminOrderProductPriceSelector).clone().children().remove().end().text());
				
				products.push(product);
			});

			switch (orderStatus) {
				case 'wc-completed':
					// purchase info object that will be pushed into dataLayer
					const purchaseInfo = {
						event: 'purchase',
						ecommerce: {
							currency: btWooGlobals.currencyCode,
							value: parseFloat($('.wc-order-totals').first().find('tbody tr:last-child .total > span > bdi').clone().children().remove().end().text()),
							transaction_id: orderId,
							items: products
						}
					};
					
					$('.wc-order-totals').first().find('tbody tr').map((i, e) => {
						const $this = $(e);

						switch ($this.find('> .label').text()) {
							case 'משלוח:': if ($this.find('.woocommerce-Price-amount').length > 0) purchaseInfo.ecommerce.shipping = parseFloat($this.find('.total > span > bdi').clone().children().remove().end().text()); break;
							case 'סך הכל:': if ($this.find('.includes_tax').length > 0) purchaseInfo.ecommerce.tax = parseFloat($this.find('.includes_tax > span').clone().children().remove().end().text()); break;
						}
					});

					if (displayConsoleLog) console.log(purchaseInfo);

					// push purchase info
					dataLayer.push(purchaseInfo);

					break;
				case 'wc-refunded':
					// refund info object that will be pushed into dataLayer
					const refundInfo = {
						event: 'refund',
						ecommerce: {
							transaction_id: orderId,
						}
					};
					
					if (displayConsoleLog) console.log(refundInfo);

					// push refund info
					dataLayer.push(refundInfo);
					
					break;
			}
			
			// set/update current order status
			adminOrdersStatuses[orderId] = orderStatus;

			// set the new orders statuses json object
			localStorage.setItem('adminOrdersStatuses', JSON.stringify(adminOrdersStatuses));
		})(adminOrdersStatuses, orderIdSelector);
	}

	//===============================================================
	// ADMIN - END
	//===============================================================
})(jQuery);