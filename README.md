# magento-2-designer

Magento 2 Designer Extension FREE
It allow admin manage designer on backend, assign products to designer, filter designer on frontend, and view designer info on product detail page

**Example view:**
- https://onwood.vn/nha-thiet-ke
- https://www.carlhansen.com/en/designers
- https://www.dwr.com/subdesigners?lang=en_US

# Setup

 1. Setup via composer

Setup the module via composer:

    composer require landofcoder/module-designer
    php bin/magento module:enable Lof_Designer
    php bin/magento setup:upgrade
    php bin/magento setup:static-content:deploy -f

## Preview

1. All Designer Page

![All Designer Page](images/all_designer_page.png)

2. View Detial Designer Page

![View Detail Designer Page](images/view_detail_designer_page.png)

3. Designer Info On Product Page

![Designer Info on Product Page](images/product_detail_designer.png)