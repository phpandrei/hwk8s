# HELLO WORLD AND K8S :)

Слов много: митап с воркшопом, где разберем кейсы,а в конце получим ценный экспириенс. ?

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

```docker build -t my-gogo-app .```

проверим
```docker run -p 8080:8080 my-gogo-app```
в браузере http://127.0.0.1:8080/test?abc=444444&cde=5555

```sudo docker tag my-gogo-app_v1 localhost:5000/my-gogo-app```
```sudo docker push localhost:5000/my-gogo-app_v1```

```microk8s kubectl apply -f hwk8s_gogo_dep_ms.yaml```

## компилятор GO

```microk8s kubectl apply -f hwk8s_gogo_dep.yaml```

## логи и дебаг

```microk8s kubectl describe deployment -n hwk8s-ns hwk8s-gogo-dep```
```microk8s kubectl describe pod -n hwk8s-ns hwk8s-gogo-dep-74f5c4dfb5-6gggs```
```microk8s kubectl logs -n hwk8s-ns  hwk8s-gogo-dep-6cb8477546-8zctl```

```microk8s kubectl debug -it --image=golang:latest -n hwk8s-ns hwk8s-gogo-dep-5875d699cc-z7f7q```
```microk8s kubectl exec -it -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-569df99f6b-qcw4b -- bash go run main.go```
```microk8s kubectl exec -n hwk8s-ns -c hwk8s-gogo-pod pods/hwk8s-gogo-dep-569df99f6b-qcw4b -- "go run main.go"```

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