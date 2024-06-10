package main

import     "fmt"
import "net/http"

func handler(w http.ResponseWriter, r *http.Request) {
  fmt.Fprintln(w, "Hello world 1!!!")
  fmt.Fprintln(w, r)
  myQueryParam := r.URL.Query()
  fmt.Fprintln(w, r.URL.Query())
  fmt.Fprintln(w, myQueryParam["abc"])

  for k, v := range myQueryParam { 
    fmt.Printf("key[%s] value[%s]\n", k, v)
    fmt.Fprintln(w, "key[%s] value[%s]\n", k, v)
  }

  for k := range myQueryParam {
    fmt.Fprintln(w, "key[%s] value[%s]\n", k, myQueryParam[k])
  }
}

func main() {
  http.HandleFunc("/test", handler)
  http.ListenAndServe(":8080", nil)
}