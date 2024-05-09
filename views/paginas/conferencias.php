<main class="agenda">
    <h2 class="agenda__heading"><?php echo $titulo; ?></h2>
    <p class="agenda__descripcion">Talleres y Conferencias dictados por expertos en Desarrollo Web</p>

    <div class="eventos">
        <h3 class="eventos__heading">&lt;Conferencias /></h3>
        <!-- /heading -->

        <p class="eventos__fecha">Viernes 5 de Octubre</p>

        <div class="eventos__listado slider swiper">
            <div class="swiper-wrapper">
                <?php foreach ($eventos['conferencias_v'] as $evento) { ?>
                    <div class="evento swiper-slide">
                        <p class="evento__hora"><?php echo $evento->hora->hora; ?></p>

                        <div class="evento__informacion">
                            <h4 class="evento__nombre"><?php echo $evento->nombre; ?> </h4>

                            <p class="evento__introduccion"><?php echo $evento->descripcion; ?></p>

                            <div class="evento__autor-info">
                                <picture>
                                    <source type="image/webp" srcset="<?php echo $_ENV['HOST'] . '/img/speakers/' . $evento->ponente->imagen; ?>.webp">
                                    <source type="image/png" srcset="<?php echo $_ENV['HOST'] . '/img/speakers/' . $evento->ponente->imagen; ?>.png">
                                    <img class="evento__imagen-autor" loading="lazy" width="200" height="300" src="<?php echo $_ENV['HOST'] . '/img/speakers/' . $evento->ponente->imagen; ?>.png" alt="Imagen Ponente">
                                </picture>

                                <p class="evento__autor-nombre">
                                    <?php echo $evento->ponente->nombre . " " .$evento->ponente->apellido; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <!-- /swiper-wrapper -->

            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <!-- /Botones de navegacion del slider -->

        </div>
        <!-- /evento__listado -->

        <p class="eventos__fecha">Sabado 6 de Octubre</p>

        <div class="eventos__listado">
            
        </div>
        <!-- /evento -->

    </div>
    <!-- /eventos -->


    <div class="eventos eventos--workshops">
        <h3 class="eventos__heading">&lt;Workshops /></h3>
        <!-- /heading -->

        <p class="eventos__fecha">Viernes 5 de Octubre</p>

        <div class="eventos__listado">

        </div>
        <!-- /evento -->

        <p class="eventos__fecha">Sabado 6 de Octubre</p>

        <div class="eventos__listado">
            
        </div>
        <!-- /evento -->

    </div>
    <!-- /eventos--workshops -->

</main>
