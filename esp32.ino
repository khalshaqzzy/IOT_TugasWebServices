#include <WiFi.h>
#include <HTTPClient.h>

const char* ssid = "Shaquille";
const char* password = "12345678";
const char* serverName = "http://192.168.178.165/codeigniter4/public/sensor"; 

void setup() {
  Serial.begin(115200);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Menghubungkan ke WiFi...");
  }
  Serial.println("Terhubung ke WiFi");
  Serial.print("IP ESP32: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/json");

    int motion = digitalRead(27);
    String jsonData = "{\"motion_detected\":" + String(motion) + "}";
    if (motion == HIGH) {
    Serial.println("Gerakan Terdeteksi: YA");
  } else {
    Serial.println("Gerakan Terdeteksi: TIDAK");
  }

    int httpResponseCode = http.POST(jsonData);
    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.print("Response Code: ");
      Serial.println(httpResponseCode);
      Serial.println("Response: " + response);
    } else {
      Serial.print("Error on sending POST: ");
      Serial.println(httpResponseCode);
      Serial.println(http.errorToString(httpResponseCode));
    }
    http.end();
  } else {
    Serial.println("WiFi Tidak Terhubung");
  }
  delay(1000);  
}
