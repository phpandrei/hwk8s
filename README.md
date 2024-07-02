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

```microk8s kubectl apply -f hwk8s_local_volume.yaml```

## КОНФИГ

```microk8s kubectl apply -f hwk8s_nginx_conf.yaml```

## СЕКРЕТЫ

```microk8s kubectl apply -f hwk8s_db_secret.yaml```

## PHP + POSTGRES + почему не StatefulSet (приложения с сохранением состояния)

Что бы заработала БД с пыхой - надо собрать свой докер имедж и юзать его

```sudo docker run -d -p 5000:5000 --restart=always --name registry registry:2```
http://localhost:5000/v2/_catalog

```sudo docker build . --tag php_pg_v1```
```sudo docker tag php_pg_v1 localhost:5000/php_pg_v1```
```sudo docker push localhost:5000/php_pg_v1```

После этого тут(http://localhost:5000/v2/_catalog) будет образ, который мы запустим в кубах 

```microk8s kubectl apply -f hwk8s_phpfpm_dep.yaml```

```microk8s kubectl apply -f hwk8s_postgres_dep.yaml```

## Деплой

```microk8s kubectl rollout history -n hwk8s-ns deployment.apps/hwk8s-phpfpm-dep```    # Проверить историю деплоймента
```microk8s kubectl rollout undo -n hwk8s-ns deployment.apps/hwk8s-phpfpm-dep```    # Откатиться к предыдущей версии деплоймента
```microk8s kubectl rollout restart -n hwk8s-ns deployment.apps/hwk8s-phpfpm-dep``    # Плавающий рестарт Подов в деплойменте 

## сервис GO + докер слои


```sudo docker build . --tag my-gogo-app_v1```

проверим
```docker run -p 8080:8080 my-gogo-app_v1```
в браузере http://127.0.0.1:8080/test?abc=444444&cde=5555

```sudo docker tag my-gogo-app_v1 localhost:5000/my-gogo-app```
```sudo docker push localhost:5000/my-gogo-app```

```microk8s kubectl apply -f hwk8s_gogo_ms.yaml```

## компилятор GO

```microk8s kubectl apply -f hwk8s_gogo_dep.yaml```
```microk8s kubectl delete -f hwk8s_gogo_dep.yaml```


microk8s kubectl apply -f hwk8s_gogo_test_dep_correct.yaml
microk8s kubectl delete -f hwk8s_gogo_test_dep_correct.yaml

## логи и дебаг. ну и про ресурсы

```microk8s kubectl describe deployment -n hwk8s-ns hwk8s-gogo-dep```
```microk8s kubectl describe pod -n hwk8s-ns hwk8s-gogo-dep-65f4cbd447-q9457```
```microk8s kubectl logs -n hwk8s-ns hwk8s-gogo-dep-65f4cbd447-q9457```

```microk8s kubectl debug -it --image=golang:latest -n hwk8s-ns hwk8s-gogo-dep-696fd8d86c-n7fp5```
в контейнер      ```microk8s kubectl exec -it -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-758f9847dc-tczrd -- bash```
старт правильный ```microk8s kubectl exec -it -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-758f9847dc-tczrd -- go run tmp/src/server.go```
старт            ```microk8s kubectl exec -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-758f9847dc-tczrd -- go run tmp/src/server.go```
потроха          ```microk8s kubectl exec -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-758f9847dc-tczrd -- gofmt tmp/src/server.go```


процессы в поде ```microk8s kubectl exec -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-758f9847dc-tczrd -- ps axu```
убийство...     ```microk8s kubectl exec -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-758f9847dc-tczrd -- kill 1933```

Ограничть компилятор в ресурсах - плохая идея :)

microk8s kubectl get pod -n hwk8s-ns

## LENS

ИДЕ по сути

## job & cronJob

видел такое для kind: Deployment :

spec:
      containers:
        - name: queue-worker
          image: [your_registry_url]/cli:v0.0.1
          command:
            - php
          args:
            - artisan
            - queue:work
            - --queue=default
            - --max-jobs=200

и такое

apiVersion: batch/v1beta1
kind: CronJob
metadata:
  name: cron
  namespace: my-laravel-app
spec:
  concurrencyPolicy: Replace
  schedule: "*/1 * * * *"
  jobTemplate:
    spec:
      template:
        spec:
          containers:
          - image: my_laravel_app_image:latest
            name: cron
            command: ["php", "artisan", "schedule:run"]
            imagePullPolicy: Always
            envFrom:
            - configMapRef:
                name: laravel-app-config
            - secretRef:
                name: laravel-app-secret
          restartPolicy: Never