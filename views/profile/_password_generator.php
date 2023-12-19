<?php
    use webvimark\modules\UserManagement\UserManagementModule;
?>

<? if (Yii::$app->getModule('user-management')->usePasswordGenerator): ?>
    <div class="form-group">
        <button type="button" class="btn btn-secondary mr-4" data-role="generate"><?= UserManagementModule::t('back', 'Generate password') ?></button>
        <span class="badge badge-secondary mr-4" data-role="generate-value"></span>
        <span class="badge badge-secondary" data-role="notify"><?= UserManagementModule::t('back', 'Copied') ?></span>
    </div>
    <div class="d-none position-absolute fixed-top alert alert-secondary fade" role="alert" >

    </div>
    <?php
    $passwordLength = Yii::$app->getModule('user-management')->passwordGeneratorLength;
    $useSymbols = Yii::$app->getModule('user-management')->passwordGeneratorWithSymbols;
    $this->registerJs(<<<JS
        var generate = document.querySelector('[data-role="generate"]');
        var generate_value = document.querySelector('[data-role="generate-value"]');
        var password = document.querySelector('[data-role="password"]');
        var password_repeat = document.querySelector('[data-role="password-repeat"]');
        var notify = document.querySelector('[data-role="notify"]');
        var notify_timeout;
        if (generate && generate_value && password && password_repeat) {
            generate.onclick = function() {
                let generated = gen_password($passwordLength, $useSymbols);
                password.value = generated;
                password_repeat.value = generated;
                generate_value.innerText = generated;
                generate_value.click();
            }
            generate_value.onclick = function() {
                window.getSelection().removeAllRanges();
                var range = document.createRange();
                range.selectNode(generate_value);
                window.getSelection().addRange(range);
                document.execCommand("copy");
                window.getSelection().removeAllRanges();
                
                if (notify.classList.contains('d-none')) {
                    notify.classList.remove('d-none');
                }                
                if (!notify.classList.contains('show')) {
                    notify_timeout = setTimeout(function() {
                        notify.classList.add('d-none');
                    }, 1000);
                } else {
                    clearTimeout(notify_timeout);
                    notify_timeout = setTimeout(function() {
                        notify.classList.add('d-none');
                    }, 1000);
                }
            }
        }
        function gen_password(len, useSymbols){
            var symbols = '';
            if (useSymbols) {
                symbols = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!№;%:?*()_+=';
            } else {
                symbols = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            }
            var password = '';
            for (var i = 0; i < len; i++){
                password += symbols.charAt(Math.floor(Math.random() * symbols.length));     
            }
            return password;
        }
JS
    );
    ?>
<? endif; ?>