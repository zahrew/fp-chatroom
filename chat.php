<?php
session_start();

if (isset($_SESSION['username'])) {

	include 'app/db.conn.php';

	include 'app/helpers/user.php';
	include 'app/helpers/chat.php';
	include 'app/helpers/opened.php';

	include 'app/helpers/timeAgo.php';

	if (!isset($_GET['user'])) {
		header("Location: home.php");
		exit;
	}

	# gereftane data az user
	$chatWith = getUser($_GET['user'], $conn);

	if (empty($chatWith)) {
		header("Location: home.php");
		exit;
	}

	$chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $conn);

	opened($chatWith['user_id'], $conn, $chats);
	?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Chat App</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet"
			integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
		<link rel="stylesheet" href="styles/style.css">
		<link rel="icon" href="img/logo.png">
		<script src="https://kit.fontawesome.com/df683221e1.js" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

	</head>

	<body class="d-flex
															 justify-content-center
															 align-items-center
															 vh-100">
		<div class="w-400 shadow p-4 rounded">
			<a href="home.php" class="fs-4 link-dark">&#8592;</a>

			<div class="d-flex align-items-center">
				<img src="uploads/<?= $chatWith['p_p'] ?>" class="w-15 rounded-circle">

				<h3 class="display-4 fs-sm m-2">
					<?= $chatWith['name'] ?> <br>
					<div class="d-flex
																				 align-items-center" title="online">
						<?php
						if (last_seen($chatWith['last_seen']) == "Active") {
							?>
							<div class="online"></div>
							<small class="d-block p-1">Online</small>
						<?php } else { ?>
							<small class="d-block p-1">
								Last seen:
								<?= last_seen($chatWith['last_seen']) ?>
							</small>
						<?php } ?>
					</div>
				</h3>
			</div>

			<div class="shadow p-4 rounded
																	   d-flex flex-column
																	   mt-2 chat-box" id="chatBox">
				<?php
				if (!empty($chats)) {
					foreach ($chats as $chat) {
						if ($chat['from_id'] == $_SESSION['user_id']) { ?>
							<p class="rtext align-self-end border rounded p-2 mb-1">
								<?= $chat['message'] ?>
								<small class="d-block">
									<?= $chat['created_at'] ?>
								</small>
								<small>
									<i class="fa-regular fa-trash-can btn-delete" chatid="<?= $chat['chat_id'] ?>"></i>
									<i class="fa-regular fa-edit ms-2 btn-edit-pop" chatid="<?= $chat['chat_id'] ?>"
										data-bs-toggle="modal" data-bs-target="#exampleModal"></i>
								</small>
							</p>
						<?php } else { ?>
							<p class="ltext border rounded p-2 mb-1">
								<?= $chat['message'] ?>
								<small class="d-block">
									<?= $chat['created_at'] ?>
								</small>
								<small class="">
									<i class="fa-regular fa-trash-can btn-delete" chatid="<?= $chat['chat_id'] ?>"></i>
								</small>
							</p>
						<?php }
					}
				} else { ?>
					<div class="alert alert-info text-center">
						<i class="fa fa-comments d-block fs-big"></i>
						No messages yet, Start the conversation
					</div>
				<?php } ?>
			</div>
			<div class="input-group mb-3">
				<textarea cols="3" id="message" class="form-control"></textarea>
				<button class="btn btn-primary" id="sendBtn">
					<i class="fa fa-paper-plane"></i>
				</button>
			</div>

			<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<input type="text" id="message-edit">

						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary btn-edit">Save changes</button>
						</div>
					</div>
				</div>
			</div>

		</div>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
			integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
			crossorigin="anonymous"></script>

		<script>
			var scrollDown = function () {
				let chatBox = document.getElementById('chatBox');
				chatBox.scrollTop = chatBox.scrollHeight;
			}

			scrollDown();

			$(document).ready(function () {

				$("#sendBtn").on('click', function () {
					message = $("#message").val();
					if (message == "") return;

					$.post("app/ajax/insert.php",
						{
							message: message,
							to_id: <?= $chatWith['user_id'] ?>
																 },
						function (data, status) {
							$("#message").val("");
							$("#chatBox").append(data);
							scrollDown();
							location.reload(true);
						});
				});

				$(".btn-delete").on('click', function () {
					var $this = $(this)
					console.log($this.attr("chatid"));
					chat_id = $this.attr("chatid");
					console.log(chat_id);

					$.post("app/ajax/delete.php",
						{
							chat_id: chat_id
						}).done(function () {
							$this.parent().parent().remove();
							//location.reload(true);
						});
				});

				$(".btn-edit").on('click', function () {
					var $this = $(this)
					message = $("#message-edit").val();
					chat_id = $("#exampleModal").attr("chatid");
					$.post("app/ajax/edit.php",
						{
							chat_id: chat_id,
							message_edit: message

						}).done(function (data, status) {
							console.log(data);
							location.reload(true);
						});
				});

				$(".btn-edit-pop").on("click", function () {
					var $this = $(this)
					chat_id = $this.attr("chatid");
					console.log($this);
					$("#exampleModal").modal("show");
					$("#exampleModal").attr("chatid", chat_id);

				});

				//update kardane khodkare last seen har 10s
				let lastSeenUpdate = function () {
					$.get("app/ajax/update_last_seen.php");
				}
				lastSeenUpdate();

				setInterval(lastSeenUpdate, 10000);



				//  refresh chat har 0.5s
				let fechData = function () {
					$.post("app/ajax/recieveMessage.php",
						{
							id_2: <?= $chatWith['user_id'] ?>
						}).done(function (data, status) {
								$("#chatBox").append(data);
								if (data != "") {
									scrollDown();
									location.reload(true);
								}
							});


				}

				fechData();

				setInterval(fechData, 500);

			});
		</script>
	</body>

	</html>
	<?php
} else {
	header("Location: index.php");
	exit;
}
?>