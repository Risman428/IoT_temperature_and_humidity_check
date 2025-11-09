<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bootstrap demo</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
        <style>
            /* 🌈 Background gradient & card styling */
            body {
                background: linear-gradient(135deg, #4f46e5, #9333ea, #3b82f6);
                background-size: 300% 300%;
                animation: gradientShift 10s ease infinite;
                color: white;
                min-height: 100vh;
            }

            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }

            .card {
                backdrop-filter: blur(10px);
                border: none;
                border-radius: 20px;
                color: white;
                text-align: center;
                padding: 20px;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            /* 🌡️ Suhu (Temperature) */
            .card.temperature {
                background: linear-gradient(135deg, rgba(255, 100, 50, 0.7), rgba(255, 150, 80, 0.6));
                box-shadow: 0 8px 32px rgba(255, 120, 60, 0.3);
            }

            /* 💧 Kelembaban (Humidity) */
            .card.humidity {
                background: linear-gradient(135deg, rgba(0, 150, 255, 0.6), rgba(80, 220, 255, 0.5));
                box-shadow: 0 8px 32px rgba(0, 180, 255, 0.3);
            }

            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            }

            h1 {
                font-weight: 700;
                text-shadow: 2px 2px 8px rgba(0,0,0,0.4);
            }
        </style>
    </head>
    <body>
        <div class="container py-5">
            <h1 class="text-center mb-3">Monitoring DHT22 Sensor</h1>
            <div class="row justify-content-center">
                <div class="card col-3 mx-2 temperature">
                    <h5>Temperature</h5>
                    <p><span id="temperature"></span> °C</p>
                </div>
                <div class="card col-3 mx-2 humidity">
                    <h5>Humidity</h5>
                    <p><span id="humidity"></span> %</p>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function () {
                function getData() {
                    $.ajax({
                        type: "GET",
                        url: "/get-data",
                        success: function (response) {
                            let temperature = response.temperature;
                            let humidity = response.humidity;
                            $("#temperature").text(temperature);
                            $("#humidity").text(humidity);
                        }
                    });
                }
                setInterval(() => {
                    getData();
                }, 2000);
            });
        </script>
    </body>
</html>
