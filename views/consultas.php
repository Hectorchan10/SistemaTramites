<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas de Trámites</title>
    <link rel="stylesheet" href="/style/style.css" />
</head>
<body>
   <header>
    <div class="topbar" role="navigation" aria-label="Barra de navegación principal">
      <div class="logo">
        <a href="/index.php">
          <h1>Tramites</h1>
        </a>
      </div>
      <nav>
        <ul>
          <li><a href="/index.php">Inicio</a></li>
          <li><a href="/index.php#contacto">Contacto</a></li>
          <li><a href="/views/login.php">Login</a></li>
        </ul>
      </nav>
    </div>
  </header>
    <main>
        <div class="form-container">
            <h2 class="form-title">Consulta de Trámites</h2>
            <form id="consultaForm" action="/procesar_consulta.php" method="POST" novalidate>
                <div class="form-group">
                    <label for="dpi">Número de DPI <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="dpi" 
                        name="dpi" 
                        placeholder="Ingrese su número de DPI (13 dígitos)" 
                        pattern="\d{13}" 
                        maxlength="13"
                        required
                        aria-required="true"
                        aria-describedby="dpiHelp dpiError"
                    >
                    <div id="dpiError" class="error-message" role="alert"></div>
                    <small id="dpiHelp" class="form-text">Ingrese los 13 dígitos de su DPI sin espacios ni guiones.</small>
                </div>
                
                <div class="form-group">
                    <label for="codigo">Código de Trámite <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="codigo" 
                        name="codigo" 
                        placeholder="Ingrese el código de su trámite" 
                        required
                        aria-required="true"
                        aria-describedby="codigoError"
                    >
                    <div id="codigoError" class="error-message" role="alert"></div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span class="button-text">Buscar Trámite</span>
                    <span class="button-loading" aria-hidden="true" style="display: none;">Buscando...</span>
                </button>
                
                <div class="form-footer">
                    <p>¿No tienes un código? <a href="/solicitar_tramite.php">Solicitar nuevo trámite</a></p>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('consultaForm');
            const dpiInput = document.getElementById('dpi');
            const codigoInput = document.getElementById('codigo');
            const dpiError = document.getElementById('dpiError');
            const codigoError = document.getElementById('codigoError');

            // Validación de DPI
            dpiInput.addEventListener('input', function(e) {
                // Solo permitir números
                this.value = this.value.replace(/\D/g, '');
                
                // Validar longitud
                if (this.value.length > 13) {
                    this.value = this.value.slice(0, 13);
                }
                
                // Validar y mostrar mensaje de error
                if (this.validity.valueMissing) {
                    showError(dpiError, 'El DPI es requerido');
                } else if (this.validity.patternMismatch) {
                    showError(dpiError, 'El DPI debe tener 13 dígitos');
                } else {
                    hideError(dpiError);
                }
            });

            // Validación de código
            codigoInput.addEventListener('input', function() {
                if (this.validity.valueMissing) {
                    showError(codigoError, 'El código es requerido');
                } else {
                    hideError(codigoError);
                }
            });

            // Manejo del envío del formulario
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let isValid = true;
                
                // Validar DPI
                if (dpiInput.validity.valueMissing) {
                    showError(dpiError, 'El DPI es requerido');
                    isValid = false;
                } else if (dpiInput.validity.patternMismatch) {
                    showError(dpiError, 'El DPI debe tener 13 dígitos');
                    isValid = false;
                }
                
                // Validar código
                if (codigoInput.validity.valueMissing) {
                    showError(codigoError, 'El código es requerido');
                    isValid = false;
                }
                
                if (isValid) {
                    // Mostrar indicador de carga
                    const submitButton = form.querySelector('button[type="submit"]');
                    const buttonText = submitButton.querySelector('.button-text');
                    const buttonLoading = submitButton.querySelector('.button-loading');
                    
                    buttonText.style.display = 'none';
                    buttonLoading.style.display = 'inline';
                    submitButton.disabled = true;
                    
                    // Simular envío (reemplazar con envío real)
                    setTimeout(() => {
                        // Aquí iría el envío real del formulario
                        form.submit();
                    }, 1000);
                }
            });
            
            function showError(element, message) {
                element.textContent = message;
                element.style.display = 'block';
                element.setAttribute('aria-live', 'assertive');
            }
            
            function hideError(element) {
                element.textContent = '';
                element.style.display = 'none';
                element.removeAttribute('aria-live');
            }
        });
    </script>
</body>
</html>
