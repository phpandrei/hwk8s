package main

import     "fmt"
import "net/http"

func handler(w http.ResponseWriter, r *http.Request) {
  fmt.Fprintln(w, "Hello world from GO + K8S!!!")
}

func main() {
  http.HandleFunc("/test", handler)
  http.ListenAndServe(":8080", nil)
}