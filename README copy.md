# PHP + NGINX + POSTGRES + K8S + HELM :)

## !!!!!!!!не делаем name больше 15 символов!!!!!!

## не в тему, но когда отрубилося дашборд + sudo snap install microk8s --classic не ставился, то помогло snap revert snapd
## ip route -n
## ip route
## ip routel
## ip ip route

## sudo microk8s refresh-certs --cert ca.crt - если посыпались серты

## sudo snap remove microk8s --purge
## sudo snap install microk8s --classic

## Дашборд
**Дашборд** - ```microk8s dashboard-proxy```

В фильтре обязательно делаем показывать все неймспейсы

microk8s status --wait-ready

microk8s kubectl get all --all-namespaces

## Неймспейс
```microk8s kubectl apply -f first8ks_namespace.yaml```

```microk8s kubectl delete -f first8ks_namespace.yaml```

## Под (так делать не надо, но проверим именно так)
```microk8s kubectl apply -f first8ks_nginx_test_pod.yaml```

```microk8s kubectl delete -f first8ks_nginx_test_pod.yaml```

```microk8s kubectl get pod -n f8ks-ns```

```microk8s kubectl port-forward -n f8ks-ns f8ks-nginx-pod  8877:80```

```http://localhost:8877/```

```microk8s kubectl delete pod -n f8ks-ns f8ks-nginx-pod```

## Service (поды могут общаться через сервис, даже имея разные неймспейсы. Есть разные типы портов. мы используем NodePort с портами 30000–32767)

### NGINX POD
```microk8s kubectl apply -f first8ks_nginx_service_nginx_pod.yaml```

```microk8s kubectl delete -f first8ks_nginx_service_nginx_pod.yaml```

```http://localhost:30050/```

### NGINX SERVICE DEP
```microk8s kubectl apply -f first8ks_nginx_service.yaml```

```microk8s kubectl delete -f first8ks_nginx_service.yaml```

```http://localhost:30050/```

### PHP SERVICE DEP

```microk8s kubectl apply -f first8ks_phpfpm_service.yaml```

```microk8s kubectl apply -f first8ks_nginx_service_nginx_pod.yaml```

## Deployment nginx

```microk8s kubectl apply -f first8ks_nginx_test_dep.yaml```

```microk8s kubectl delete -f first8ks_nginx_test_dep.yaml``` 
или
```microk8s kubectl delete deployment -n f8ks-ns f8ks-nginx-dep``` 

```microk8s kubectl get pod -n f8ks-ns```

```microk8s kubectl get deployments -n f8ks-ns```

```microk8s kubectl exec -it -n f8ks-ns pod/f8ks-nginx-dep-9bf7b48df-7xdwn sh``` - войти в под
```microk8s kubectl exec -it -n f8ks-ns pod/f8ks-nginx-dep-9bf7b48df-dlrfx -c f8ks-nginx-pod sh``` - войти в под + указали имя контейнера

f8ks-gogo-dep-76c46677cd-n8b6z

## Volume 

PersistentVolume - по сути хранилище любое
PersistentVolumeClaim - абстракция. Оно берет место из PersistentVolume. Котнейнер же, который получает PVC не знает, что за хранилище

Это может быть s3, локальная папка, кластер

```microk8s kubectl apply -f first8ks_local_volume.yaml```

```microk8s kubectl delete -f first8ks_local_volume.yaml```

```echo "local srorage test" >> local.txt```

## Deployment php    
```microk8s kubectl apply -f first8ks_phpfpm_test_dep.yaml```

```microk8s kubectl delete -f first8ks_phpfpm_test_dep.yaml```

## Config nginx

```microk8s kubectl apply -f first8ks_nginx_conf.yaml```

```microk8s kubectl delete -f first8ks_nginx_conf.yaml```

Замечание по портам:

```fastcgi_pass f8ks-phpfpm-s:9000;```
f8ks-phpfpm-s - это сервис, который работает с подом php



microk8s kubectl apply -f first8ks_nginx_conf.yaml
microk8s kubectl apply -f first8ks_nginx_test_dep.yaml
microk8s kubectl apply -f first8ks_phpfpm_test_dep.yaml

microk8s kubectl delete -f first8ks_nginx_test_dep.yaml
microk8s kubectl delete -f first8ks_phpfpm_test_dep.yaml
microk8s kubectl delete -f first8ks_nginx_conf.yaml


microk8s kubectl apply -f first8ks_localdb_volume.yaml
microk8s kubectl apply -f first8ks_local_volume.yaml
microk8s kubectl apply -f first8ks_gogo_volume.yaml
microk8s kubectl delete -f first8ks_localdb_volume.yaml
microk8s kubectl delete -f first8ks_local_volume.yaml
microk8s kubectl delete -f first8ks_gogo_volume.yaml


microk8s kubectl apply -f first8ks_postgres_test_dep.yaml

microk8s kubectl logs -f -n f8ks-ns f8ks-db-dep-6558b555df-vn452



## немного локального регистри для докер образов
sudo docker run -d -p 5000:5000 --restart=always --name registry registry:2
http://localhost:5000/v2/_catalog

sudo docker build . --tag php_pg_v1

команда (например): sudo docker tag microsevice_v1 <host ip>:5000/microsevice_v1

Данной командой мы добавили тег для локального образа. Формат тега:

<hostname | ip >:post/<image name>:<tag>

sudo docker tag php_pg_v1 localhost:5000/php_pg_v1
sudo docker push localhost:5000/php_pg_v1

sudo docker tag gofirst_golang_first_v1 localhost:5000/gofirst_golang_first

sudo docker push localhost:5000/gofirst_golang_first_v1


## для клиента БД постгреса

microk8s kubectl get all -n f8ks-ns

microk8s kubectl rollout history -n f8ks-ns deployment.apps/f8ks-db-dep    # Проверить историю деплоймента
microk8s kubectl rollout undo -n f8ks-ns deployment.apps/f8ks-db-dep        # Откатиться к предыдущей версии деплоймента
microk8s kubectl rollout restart -n f8ks-ns deployment.apps/f8ks-db-dep     # Плавающий рестарт Подов в деплойменте 

microk8s kubectl apply -f first8ks_phpfpm_test_dep.yaml

microk8s kubectl delete -f first8ks_phpfpm_test_dep.yaml

microk8s kubectl rollout history -n f8ks-ns deployment.apps/f8ks-phpfpm-dep    # Проверить историю деплоймента
microk8s kubectl rollout undo -n f8ks-ns deployment.apps/f8ks-phpfpm-dep       # Откатиться к предыдущей версии деплоймента
microk8s kubectl rollout restart -n f8ks-ns deployment.apps/f8ks-phpfpm-dep     # Плавающий рестарт Подов в деплойменте 

логин и пароль брался с доки

host=127.0.0.1
port=30051
dbname=test
user=postgres
password=qwerty

## посмотреть логи

microk8s kubectl logs -f -n f8ks-ns f8ks-db-dep-6558b555df-vn452

## Secret

microk8s kubectl apply -f first8ks_db_secret.yaml
microk8s kubectl delete -f first8ks_db_secret.yaml


## HELM

sudo snap install helm --classic

helm create test-chart

microk8s helm install my-helm-release  test-chart -n tst-namespace -f test-chart/values.yaml

microk8s helm install my-helm-release  test-chart -f test-chart/values.yaml

microk8s kubectl get all --all-namespaces

С неймспесами (но неймспейсы создавать заранее)
microk8s helm install my-helm-release-prod test-chart -n f8ks-helm-prod-ns -f test-chart/values.yaml
microk8s helm install my-helm-release-dev test-chart -n f8ks-helm-dev-ns -f test-chart/dev.yaml

microk8s helm upgrade -i test-chart - были проблемы. После этого вроде как запустилось

Неймспейсы в переменной (но неймспейсы создавать заранее)
microk8s helm install my-helm-release-prod test-chart -f test-chart/values.yaml
microk8s helm install my-helm-release-dev test-chart -f test-chart/dev.yaml

microk8s helm uninstall my-helm-release-prod -n f8ks-helm-prod
microk8s helm uninstall my-helm-release-dev -n f8ks-helm-dev

microk8s helm uninstall my-helm-release-prod
microk8s helm uninstall my-helm-release-dev

Может даже удалять лучше без неймспейсов. Ну или следить за ними

microk8s kubectl apply -f first8ks_namespace_helm_dev.yaml
microk8s kubectl apply -f first8ks_namespace_helm_prod.yaml
microk8s kubectl delete -f first8ks_namespace_helm_dev.yaml
microk8s kubectl delete -f first8ks_namespace_helm_prod.yaml

упаковать - ```helm package test-chart```

unalias kubectl

alias kubectl='microk8s kubectl'

## GOGO

microk8s kubectl apply -f first8ks_gogo_test_dep.yaml

microk8s kubectl delete -f first8ks_gogo_test_dep.yaml

microk8s kubectl describe deployment -n gogo-ns f8ks-gogo-dep

microk8s kubectl describe deployment -n gogo-ns f8ks-gogo-dep


microk8s kubectl describe pod -n gogo-ns f8ks-gogo-dep-7bc45dc776-wthr7

microk8s kubectl logs -f -n gogo-ns f8ks-gogo-dep-7bc45dc776-wthr7

kubectl describe pods -n namespace 


      volumes:
        - name: f8ks-gogo-pvc-v
          persistentVolumeClaim:
            claimName: f8ks-gogo-local-pvc
          ports:
            - containerPort: 80
          volumeMounts:
            - name: f8ks-gogo-pvc-v
              mountPath: /go/tmp/src
---
apiVersion: v1
kind: Service
metadata:
  name: f8ks-gogo-s
  namespace: f8ks-ns
spec:
  type: NodePort
  selector:
    app: f8ks-gogo
  ports:
    - port: 80
      nodePort: 30055 #для внешнего подключения



          resources:
            requests:
              memory: "100Mi"
              cpu: "250m"
            limits:
              memory: "200Mi"
              cpu: "500m"      

                        lifecycle:
            postStart:
              exec:
                command: ["/bin/sh", "-c", "echo Hello from the postStart handler > /usr/share/message"]
            preStop:
              exec:
                command: ["/bin/sh","-c","nginx -s quit; while killall -0 nginx; do sleep 1; done"]    


Поймал проблему при запуске гого

логов не было. 

В итоге нашел в events:

https://127.0.0.1:10443/#/event?namespace=_all


так же команда microk8s kubectl describe no

The Deployment "f8ks-gogo-dep" is invalid: spec.template.spec.containers[0].imagePullPolicy: Unsupported value: "ifNotPresent": supported values: "Always", "IfNotPresent", "Never"



microk8s kubectl apply -f first8ks_namespace.yaml
microk8s kubectl apply -f first8ks_local_volume.yaml
microk8s kubectl apply -f first8ks_localdb_volume.yaml
microk8s kubectl apply -f first8ks_db_secret.yaml
microk8s kubectl apply -f first8ks_nginx_conf.yaml
microk8s kubectl apply -f first8ks_nginx_service.yaml
microk8s kubectl apply -f first8ks_phpfpm_service.yaml
microk8s kubectl apply -f first8ks_nginx_test_dep.yaml
microk8s kubectl apply -f first8ks_phpfpm_test_dep.yaml
microk8s kubectl apply -f first8ks_postgres_test_dep.yaml


microk8s kubectl delete -f first8ks_namespace.yaml
microk8s kubectl delete -f first8ks_local_volume.yaml
microk8s kubectl delete -f first8ks_localdb_volume.yaml
microk8s kubectl delete -f first8ks_db_secret.yaml
microk8s kubectl delete -f first8ks_nginx_conf.yaml
microk8s kubectl delete -f first8ks_nginx_service.yaml
microk8s kubectl delete -f first8ks_phpfpm_service.yaml
microk8s kubectl delete -f first8ks_nginx_test_dep.yaml
microk8s kubectl delete -f first8ks_phpfpm_test_dep.yaml
microk8s kubectl delete -f first8ks_postgres_test_dep.yaml


microk8s kubectl apply -f first8ks_namespace.yaml


microk8s kubectl apply -f first8ks_gogo_volume_pv.yaml
microk8s kubectl apply -f first8ks_gogo_volume_pvc.yaml

microk8s kubectl apply -f first8ks_gogo_test_dep.yaml
microk8s kubectl delete -f first8ks_gogo_test_dep.yaml
microk8s kubectl get pod -n f8ks-ns

microk8s kubectl describe deployment -n f8ks-ns f8ks-gogo-dep
microk8s kubectl describe pod -n f8ks-ns f8ks-gogo-dep-74f5c4dfb5-6gggs
microk8s kubectl logs -n f8ks-ns  f8ks-gogo-dep-6cb8477546-8zctl


docker build -t my-gogo-app .

проверим
docker run -p 8080:8080 my-gogo-app
в браузере http://127.0.0.1:8080/test?abc=444444&cde=5555

sudo docker tag my-gogo-app_v1 localhost:5000/my-gogo-app
sudo docker push localhost:5000/my-gogo-app_v1

microk8s kubectl apply -f first8ks_gogo_test_dep_ms.yaml
microk8s kubectl delete -f first8ks_gogo_test_dep_ms.yaml

microk8s kubectl apply -f first8ks_gogo_test_dep.yaml
microk8s kubectl delete -f first8ks_gogo_test_dep.yaml
microk8s kubectl apply -f first8ks_gogo_test_dep.yaml

microk8s kubectl describe pod -n f8ks-ns f8ks-gogo-dep-56954bbb8c-226dh

 microk8s kubectl debug -it --image=golang:latest -n f8ks-ns f8ks-gogo-dep-6d584b85cb-j7slm

 microk8s kubectl logs -n f8ks-ns f8ks-db-dep
 microk8s kubectl logs -n f8ks-ns f8ks-gogo-dep-8449cfd45-fdjqq -p --previous
microk8s kubectl get events --sort-by=.metadata.creationTimestamp 
microk8s kubectl get events -n f8ks-ns


microk8s kubectl exec -it -n f8ks-ns -c f8ks-gogo-pod pods/f8ks-gogo-dep-8449cfd45-fsc8c -- bash

microk8s kubectl debug -it --attach=false -c debugger --image=busybox -n f8ks-ns f8ks-gogo-dep-8449cfd45-fsc8c

microk8s kubectl attach -it -c debugger -n f8ks-ns f8ks-gogo-dep-8449cfd45-fsc8c

microk8s kubectl logs -n f8ks-ns f8ks-gogo-dep-84d977984c-tdsbr --all-containers

microk8s kubectl describe replicasets.apps -n f8ks-ns f8ks-gogo-dep

microk8s kubectl get events -n f8ks-ns --sort-by=.metadata.creationTimestamp

 microk8s kubectl exec -it -n f8ks-ns f8ks-gogo-dep-84d977984c-tdsbr -c f8ks-gogo-pod sh
 microk8s kubectl get pods -n f8ks-ns f8ks-gogo-dep-84d977984c-tdsbr -o jsonpath='{.spec.containers[*].name}'
  microk8s kubectl get pod -o="custom-columns=NAME:.metadata.name,INIT-CONTAINERS:.spec.initContainers[*].name,CONTAINERS:.spec.containers [*].name"


microk8s kubectl get log pod f8ks-gogo-dep-5875d699cc-z7f7q -n f8ks-ns
microk8s kubectl logs -n f8ks-ns f8ks-gogo-dep-5875d699cc-z7f7q
microk8s kubectl debug -it --image=golang:latest -n f8ks-ns f8ks-gogo-dep-5875d699cc-z7f7q
microk8s kubectl exec -it -n f8ks-ns -c f8ks-gogo-pod pods/f8ks-gogo-dep-569df99f6b-qcw4b -- bash go run main.go
microk8s kubectl exec -n f8ks-ns -c f8ks-gogo-pod pods/f8ks-gogo-dep-569df99f6b-qcw4b -- "go run main.go"
f8ks-gogo-dep-569df99f6b-qcw4b
docker-compose exec golang_first go run server.go


```microk8s kubectl exec -n f8ks-ns -c f8ks-gogo-pod pods/f8ks-gogo-dep-569df99f6b-qcw4b -- go run tmp/src/main.go```

## LENS

получение конфига для ленс

microk8s config

он даст код большой. Вероятно присмене сертоф - мы должны его поменять

потом идём в добавление кластера и туда вставляем это всё. Ну и коннектимся
