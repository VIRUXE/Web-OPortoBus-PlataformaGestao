		</div>
		<!-- /.container-fluid -->

		</div>
		<!-- End of Main Content -->

		<!-- Footer -->
		<!-- <footer class="sticky-footer bg-white">
			<div class="container my-auto">
				<div class="copyright text-center my-auto">
					<span>Copyright &copy; OPortoBus.pt 2020</span>
				</div>
			</div>
		</footer> -->
		<!-- End of Footer -->

		</div>
		<!-- End of Content Wrapper -->

		<!-- Bootstrap core JavaScript-->
		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

		<!-- Core plugin JavaScript-->
		<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

		<!-- Custom scripts for all pages-->
		<script src="js/sb-admin-2.min.js"></script>

		<!-- Page level plugins -->
		<script src="vendor/chart.js/Chart.min.js"></script>

		<!-- Page level custom scripts -->
		<script src="js/demo/chart-area-demo.js"></script>
		<script src="js/demo/chart-pie-demo.js"></script>

		<script src="http://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>

		<script type="text/javascript">
			window.addEventListener("load", function() 
			{
				var now = new Date();
				var utcString = now.toISOString().substring(0,19);
				var year = now.getFullYear();
				var month = now.getMonth() + 1;
				var day = now.getDate();
				var hour = now.getHours();
				var minute = now.getMinutes();
				var second = now.getSeconds();
				var localDatetime = year + "-" +
									(month < 10 ? "0" + month.toString() : month) + "-" +
									(day < 10 ? "0" + day.toString() : day) + "T" +
									(hour < 10 ? "0" + hour.toString() : hour) + ":" +
									(minute < 10 ? "0" + minute.toString() : minute) +
									utcString.substring(16,19);
				document.getElementById("dataInicio").value = localDatetime;
				document.getElementById("dataFim").value = localDatetime;

				getLocation();
			});
		</script>
		<script type="text/javascript">
			function isNumberKey(evt, element) {
				var charCode = (evt.which) ? evt.which : event.keyCode
				if (charCode > 31 && (charCode < 48 || charCode > 57) && !(charCode == 46 || charCode == 8))
				return false;
				else {
				var len = $(element).val().length;
				var index = $(element).val().indexOf('.');
				if (index > 0 && charCode == 46) {
					return false;
				}
				if (index > 0) {
					var CharAfterdot = (len + 1) - index;
					if (CharAfterdot > 3) {
					return false;
					}
				}

				}
				return true;
			}
		</script>
		<script>
		var x = document.getElementById("localizacao");
		
		function getLocation()
		{
			if (navigator.geolocation)
				navigator.geolocation.getCurrentPosition(showPosition);
			else
				alert("Geolocation is not supported by this browser.");
		}

		function showPosition(position) {
			x.setAttribute("value", position.coords.latitude +	"," + position.coords.longitude);
		}

		getLocation();

		$(function () {
			$('[data-toggle="popover"]').popover({
				delay: { "show": 250, "hide": 250 }
			})
		})
		</script>
	</body>
</html>
