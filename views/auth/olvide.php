<main class="auth">
    <h2 class="auth__heading"><?php echo $titulo ?></h2>
    <p class="auth__texto">Recuperar cuenta DevWebcamp</p>

    <?php
        require_once __DIR__. '/../templates/alertas.php';
    ?>

    <form method="POST" action="/olvide" class="formulario">
        <div class="formulario__campo">
            <label for="email" class="formulario__label">Email</label>
            <input 
                type="email"
                class="formulario__input"
                placeholder="Tu Email"
                id="email"
                name="email"
            >
        </div>

        <input type="submit" class="formulario__submit" value="Enviar Instrucciones">
    </form>
    <!-- /Formulario de Inicio de Sesión -->

    <div class="acciones">
        <a href="/registro" class="acciones__enlace">¿Aun no tienes cuenta? Obtener una</a>
        <a href="/login" class="acciones__enlace">¿Ya tienes cuenta? Inicia sesión</a>
    </div>
</main>