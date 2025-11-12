#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>  //connecting webserver
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <DHT_U.h>

//confiruration wifi
const char* ssid = "Y30i";
const char* password = "12345678";
//pastikan NodeMCU dan laptop memakai jaringan yang sama


#define DHTPIN D1  // Digital pin connected to the DHT sensor | perubahan 2 => D3

#define DHTTYPE DHT22  // DHT 22 (AM2302)

DHT_Unified dht(DHTPIN, DHTTYPE);

uint32_t delayMS;

void setup() {
  Serial.begin(115200);
  delay(1000);

  // Connection to WiFi
  Serial.println();
  Serial.println("Menghubungkan ke WiFi: ");
  Serial.println(ssid);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println();
  Serial.println("WiFi is Connected");
  Serial.print("IP Adress : ");
  Serial.println(WiFi.localIP());  //untuk mengetahui IP dari NodeMCU nya

  // Initialize device.
  dht.begin();
  sensor_t sensor;
  dht.temperature().getSensor(&sensor);
  dht.humidity().getSensor(&sensor);
  delayMS = 2000;
}

void loop() {
  // Delay between measurements.
  delay(delayMS);
  // Get temperature event and print its value.
  sensors_event_t event;

  //read temperature
  dht.temperature().getEvent(&event);
  float temperature = event.temperature;

  //read humidity
  dht.humidity().getEvent(&event);
  float humidity = event.relative_humidity;

  //pengecekan DHT
  if (isnan(temperature) || isnan(humidity)) {
    Serial.println("Gagal membaca sensor DHT");
    return;
  }

  Serial.print("Temperature: ");
  Serial.print(temperature);
  Serial.println(" °C");

  Serial.print("Humidity: ");
  Serial.print(humidity);
  Serial.println(" %");

  //sending data to Laravel
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    WiFiClient client;

      //gunakan IP adress laptop sendiri
      String url = "http://10.222.140.49/dhtiot/public/update-data/";
    url += String(temperature, 1) + "/" + String(humidity, 1);

    Serial.print("Mengirim data ke : ");
    Serial.println(url);

    http.begin(client, url);
    int httpCode = http.GET();

    if (httpCode > 0) {
      Serial.printf("HTTP Respone Code : %d\n", httpCode);
      String payload = http.getString();
      Serial.println("Response : ");
      Serial.println(payload);
    } else {
      Serial.printf("Gagal Mengirim data. Error : %s\n", http.errorToString(httpCode).c_str());
    }

    http.end();
  } else {
    Serial.println("WiFi tidak terkoneksi");
    WiFi.reconnect();
  }

  delay(3000);
}
