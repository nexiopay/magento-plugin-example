define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';

        rendererList.push(
            {
                type: 'nexio',
                component: 'Nexio_Payment/js/view/payment/method-renderer/iframe'
            }
        );

        return Component.extend({});
    }
);
