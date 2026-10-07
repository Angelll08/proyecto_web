<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Chat Grupal Temporal</title>
	<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
	<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
	<script>
		$(document).bind("mobileinit", function(){
			$.mobile.ajaxEnabled = false;
		});
	</script>
	<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
	<style>
		.chat-container {
			max-width: 650px;
			margin: 0 auto;
			background: #f7f9fb;
			border-radius: 12px;
			box-shadow: 0 4px 15px rgba(0,0,0,0.1);
			overflow: hidden;
			display: flex;
			flex-direction: column;
			height: 80vh;
		}
		.chat-header {
			background: #2c3e50;
			color: #ffffff;
			padding: 12px 20px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.status-badge {
			font-size: 0.8rem;
			background: #27ae60;
			padding: 4px 8px;
			border-radius: 12px;
			color: #fff;
		}
		.chat-box {
			flex: 1;
			padding: 15px;
			overflow-y: auto;
			display: flex;
			flex-direction: column;
			gap: 10px;
			background: #ffffff;
		}
		.message-bubble {
			max-width: 75%;
			padding: 10px 14px;
			border-radius: 15px;
			font-size: 0.95rem;
			line-height: 1.4;
			box-shadow: 0 1px 3px rgba(0,0,0,0.08);
			word-wrap: break-word;
		}
		.message-received {
			align-self: flex-start;
			background: #eef2f5;
			color: #333333;
			border-bottom-left-radius: 2px;
		}
		.message-sent {
			align-self: flex-end;
			background: #007bff;
			color: #ffffff;
			border-bottom-right-radius: 2px;
		}
		.message-info {
			font-size: 0.75rem;
			margin-bottom: 4px;
			font-weight: bold;
			opacity: 0.8;
		}
		.chat-input-area {
			padding: 12px;
			background: #f1f3f5;
			border-top: 1px solid #e0e0e0;
		}
		.form-row {
			display: flex;
			gap: 8px;
		}
		.input-nombre {
			width: 30% !important;
		}
		.input-mensaje {
			flex: 1 !important;
		}
	</style>
</head>
<body>
	<div data-role="page" id="pagina-chat">
		<div data-role="header" data-theme="b">
			<a href="index.php" data-icon="back" data-ajax="false">Volver</a>
			<h1>Chat Grupal Temporal</h1>
		</div>

		<div role="main" class="ui-content">
			<div class="chat-container">
				<div class="chat-header">
					<span style="font-weight: bold;">Chat de la Sesión</span>
					<span class="status-badge">🟢 En vivo</span>
				</div>

				<div class="chat-box" id="chatBox"></div>

				<div class="chat-input-area">
					<form id="chatForm">
						<div class="form-row">
							<div class="input-nombre">
								<input type="text" id="nombre" placeholder="Tu Nombre" required>
							</div>
							<div class="input-mensaje">
								<input type="text" id="mensaje" placeholder="Escribe un mensaje..." required autocomplete="off">
							</div>
							<div>
								<button type="submit" id="btnEnviar" data-role="button" data-inline="true" data-theme="b">Enviar</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>

	<script>
		let lastTimestamp = 0;
		const renderedMsgIds = new Set();
		const chatBox = document.getElementById('chatBox');

		function renderMessage(msg) {
			if (renderedMsgIds.has(msg.id)) return;
			renderedMsgIds.add(msg.id);

			const myName = $('#nombre').val().trim();
			const isMe = myName !== "" && msg.nombre.toLowerCase() === myName.toLowerCase();
			
			const bubble = document.createElement('div');
			bubble.className = `message-bubble ${isMe ? 'message-sent' : 'message-received'}`;
			
			bubble.innerHTML = `
				<div class="message-info">${msg.nombre} • ${msg.hora}</div>
				<div>${msg.mensaje}</div>
			`;
			
			chatBox.appendChild(bubble);
			chatBox.scrollTop = chatBox.scrollHeight;
		}

		function fetchMessages() {
			$.ajax({
				url: 'chat_stream.php',
				method: 'GET',
				data: { last: lastTimestamp },
				dataType: 'json',
				success: function(response) {
					if (response && response.length > 0) {
						response.forEach(function(msg) {
							renderMessage(msg);
							if (msg.timestamp > lastTimestamp) {
								lastTimestamp = msg.timestamp;
							}
						});
					}
				},
				complete: function() {
					// En Render se mantiene la actualización fluida cada 1.5s sin bloqueos
					setTimeout(fetchMessages, 1500);
				}
			});
		}

		$('#chatForm').on('submit', function(e) {
			e.preventDefault();
			const nombre = $('#nombre').val().trim();
			const mensaje = $('#mensaje').val().trim();

			if (!mensaje) return;

			$('#btnEnviar').prop('disabled', true);

			$.post('chat_send.php', { nombre: nombre, mensaje: mensaje }, function(response) {
				$('#mensaje').val('');
				$('#btnEnviar').prop('disabled', false);
				fetchMessages();
			}, 'json').fail(function() {
				$('#btnEnviar').prop('disabled', false);
			});
		});

		$(document).ready(function() {
			fetchMessages();
		});
	</script>
</body>
</html>
