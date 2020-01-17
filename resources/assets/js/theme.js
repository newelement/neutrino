let modalMessage = '';
let shoppeAddCartAlert = document.querySelector('#shoppe-product-alert');
let thisVariation = {};
let alertTypes = [
    'alert-primary',
    'alert-secondary',
    'alert-success',
    'alert-danger',
    'alert-warning',
    'alert-info'
];

function showShoppeAddCartMessage(type){
    shoppeAddCartAlert.classList.remove(alertTypes.join(','));
    shoppeAddCartAlert.classList.add('alert-'+type);
    shoppeAddCartAlert.innerHTML = modalMessage;
    shoppeAddCartAlert.classList.remove('d-none');
}

function closeShoppeAddCartMessage(){
    shoppeAddCartAlert.classList.add('d-none');
    shoppeAddCartAlert.classList.remove(alertTypes.join(','));
    shoppeAddCartAlert.innerHTML = '';
}

function checkArrays( arrA, arrB ){
    if(arrA.length !== arrB.length) return false;
    var cA = arrA.slice().sort().join(",");
    var cB = arrB.slice().sort().join(",");
    return cA === cB;
}

window.addEventListener('DOMContentLoaded', (e) => {

    let $productImageSelected = document.querySelector('.product-image-selected');
    let $productThumbs = document.querySelectorAll('.product-image-thumb');
    let $addCartBtn = document.querySelectorAll('.add-to-cart-btn');
    let $productAttributeList = document.querySelectorAll('.product-attribute-list');
    let $productImageLink = document.querySelector('.product-image-selected a')
    let $productPrice = document.querySelector('#price');
    let $productStock = document.querySelector('#stock');
    let $productPartNumber = document.querySelector('#mfg-part-number');
    let $variationId = document.querySelector('#variation-id');


    /*
    * PRODUCT SINGLE IMAGE GALLERY
    *
    *
    */
    if( $productThumbs ){
        $productThumbs.forEach(function(v){
            v.addEventListener('click', function(e){
                e.preventDefault();
                let medium = v.getAttribute('data-medium');
                let href = v.getAttribute('href');
                let currHeight = $productImageSelected.offsetHeight;
                $productImageSelected.style.minHeight = currHeight+'px';
                $productImageSelected.innerHTML = '<a href="'+href+'"><img src="'+medium+'" alt=""></a>';
                document.querySelector('.product-image-selected a').addEventListener('click', function(e) {
                    e.preventDefault();
                });
                $productThumbs.forEach(function(el){
                    el.classList.remove('active');
                });
                v.classList.add('active');
            });
        });
    }

    if( $productImageLink ){
        $productImageLink.addEventListener('click', function(e) {
            e.preventDefault();
        });
    }


    /*
    * PRODUCT SINGLE ADD CART BUTTON
    *
    *
    */
    if( $addCartBtn ){
        $addCartBtn.forEach(function(v){
            v.addEventListener('click', function(e){
                $hasAttributes = v.getAttribute('data-has-attributes');
                if( $hasAttributes ){
                    $productAttributeList.forEach(function(v){
                        if( v.value === '' ){
                            modalMessage = 'Please choose all product options.';
                            showShoppeAddCartMessage('warning');
                            e.preventDefault();
                        } else {
                            $variationId.value = thisVariation.id;
                        }
                    });
                }
            });
        });
    }


    /*
    * PRODUCT ATTRIBUTES AND VARIATIONS
    *
    *
    */
    $productAttributeList.forEach(function(v){
        thisVariation = {};
        v.addEventListener('change', function(e){
            //console.log('CHANGE ', e);
            let chooseAllAttributes = true;
            let attrSet = [];
            $productAttributeList.forEach(function(v){
                if( v.value === '' ){
                    chooseAllAttributes = false;
                }
                attrSet.push(v.value);
            });

            if ( chooseAllAttributes ){
                thisVariation = variations.filter( function(obj){
                    if( checkArrays( attrSet, obj.attribute_values ) ){
                        return obj;
                    }
                })[0];

                if( typeof thisVariation !== 'undefined' ){
                    if( thisVariation.price ){
                        $productPrice.innerHTML = '$'+thisVariation.price;
                    }
                    if( thisVariation.stock && $productStock ){
                        $productStock.innerHTML = thisVariation.stock;
                    }
                    if( thisVariation.mfg_part_number && $productPartNumber ){
                        $productPartNumber.innerHTML = thisVariation.mfg_part_nuber;
                    }
                    if( thisVariation.image ){
                        let $variationImage = document.querySelector('#variation-image-'+thisVariation.id);
                        let $variationImageLink = document.querySelector('#variation-image-'+thisVariation.id+' a');
                        $variationImageLink.click();
                    }
                }
            }
        });
    });

});
