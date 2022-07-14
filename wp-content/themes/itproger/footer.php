    <footer>
        <div class="background-footer">
            <div class="logotype-in-footer">
                <a href="/" class="link-logo"><?php  dynamic_sidebar( 'logotype-sidebar' ); ?></a>
            </div>
            <div class="center-footer">
                <div class="one-column-footer">
                    <h3>Проект</h3>
                    <ul>
                        <a href="<?php echo get_home_url()?>"><li>Главная</li></a>
                        <a href="/about-us"><li>О нас</li></a>
                        <a href="/contacts"><li>Контакты</li></a>
                        <a href="/privacy-policy"><li>Политика конфиденцальности</li></a>
                    </ul>
                </div>
                <div class="two-column-footer">
                    <h3>Разделы</h3>
                    <?php dynamic_sidebar('left-sidebar')?>
                </div>
            </div>
            <div class="social-network">
                <p>Присоединяйтесь к нам в социалных сетях</p>
                <div class="img-social-networks">
                    <a href=""><img src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/vk.png"); ?>" alt="VK"></a>
                    <a href=""><img src="<?php echo esc_url( get_template_directory_uri() . "/assets/img/tel.png"); ?>" alt="Telegram"></a>
                </div>
            </div>
        </div>
        <div class="copywriting">
            <p>© itstalker.ru, 2022 Все права защищены.</p>
        </div>

    </footer>
    <?php wp_footer(); ?>
</body>
</html>