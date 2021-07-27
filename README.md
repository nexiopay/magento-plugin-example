# DEPRECATED
DEPRECATED A Nexio Magento extension. Takes credit card payments directly on your Magento store using Nexio.

### Description
Accept Credit Card transaction with [Nexio's](https://nexiopay.com/) payment platform. 

### Installstion
- Copy the entire folder into your magento installpath/app/code. For example, suppose your magento installation path is /var/www/html/magento, then you should copy the extension into /var/www/html/magento/app/code/
- run php bin/magento setup:upgrade
- run php bin/magento setup:di:compile
- run php bin/magento setup:static-content:deploy -f

### Configuration
- Login your magento adminstration page.
- Selece STORES -> Configuration -> SALES -> Payment Methods.
- Find 'Nexio Payment' in OTHER PAYMENT METHODS.
- Config following parameters:
    - **Enabled**: Choose 'Yes' to active Nexio payment method.
    - **Title**: Credit Card
    - **Is Test Environment**: Choose 'Yes' if you want to do test transaction. Once you choose 'Yes', 'Username', 'Password' and 'Endpoint' will change to 'Test Username', 'Test Password' and 'Test Endpoint'.
    - **Username**/**Test Username**: Your Nexio username
    - **Password**/**Test Password**: Your Nexio password
        _(If you have questions or if you need a Nexio username and password, please contact integrations@nexiopay.com)_
    - **Endpoint**/**Test Endpoint**:
        - For testing: https://api.nexiopaysandbox.com/
        - For production: https://api.nexiopay.com/
    - **Merchant ID**: The merchant id of your Nexio account
    - **Custom CSS url**: The URL where your CSS file is hosted (required for custom CSS).
    - **Custom text file**: The URL where your custom text file is hosted.
    - **Fraud Check**: Enable fraud check through Kount.
        _(If you would like to enable Kount on your Nexio account, please contact integrations@nexiopay.com)_
    - **Require CVC**: Require CVC in Nexio form.
    - **Hide CVC**: Hide CVC.
    - **Hide Billing**: Hide billing info.
    - **Auth Only**: Make the transaction auth only.
    - **Verify Signature**: Enable or disable signature verification.

