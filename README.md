# HELLO WORLD AND K8S :)

## !!!!!!!!не делаем name больше 15 символов!!!!!!

## sudo snap install microk8s --classic

**Дашборд** - ```microk8s dashboard-proxy```

microk8s status --wait-ready

microk8s kubectl get all --all-namespaces

## НЕЙМСПЕЙСЫ

```microk8s kubectl apply -f hwk8s_namespace.yaml```

## ПОДЫ И ПОРТЫ

```microk8s kubectl apply -f hwk8s_nginx_pod.yaml```

```microk8s kubectl delete -f hwk8s_nginx_pod.yaml```

```microk8s kubectl get pod -n hwk8s-ns```

```microk8s kubectl port-forward -n hwk8s-ns hwk8s-nginx-pod  8877:80```

```http://localhost:8877/```

```microk8s kubectl delete pod -n hwk8s-ns hwk8s-nginx-pod```

```microk8s kubectl get pod -n hwk8s-ns```

### ДЕПЛОЙМЕНТ И СЕРВИСЫ


```microk8s kubectl apply -f hwk8s_nginx_dep.yaml```

```microk8s kubectl get pod -n hwk8s-ns```

```microk8s kubectl delete pod -n hwk8s-ns hwk8s-nginx-dep```

```microk8s kubectl get pod -n hwk8s-ns```

```microk8s kubectl delete -f hwk8s_nginx_dep.yaml```

```microk8s kubectl apply -f hwk8s_nginx_service.yaml```

```microk8s kubectl apply -f hwk8s_nginx_dep.yaml```

## ХРАНИЛИЩЕ