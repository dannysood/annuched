# setup locust at https://cloud.google.com/architecture/distributed-load-testing-using-gke#proxy-ssh-tunnel
import uuid
import string
import random
import json

from datetime import datetime
from locust import FastHttpUser, TaskSet, task


# [START locust_test_task]

class MetricsTaskSet(TaskSet):
    _deviceid = None

    def on_start(self):
        # self.client.DefaultRequestHeaders.TryAddWithoutValidation("Content-Type", "application/json");
        # self.client.DefaultRequestHeaders.TryAddWithoutValidation("Accept", "application/json");
        # self.client.DefaultRequestHeaders.TryAddWithoutValidation("X-Requested-With", "XMLHttpRequest");
        self._deviceid = str(uuid.uuid4())

    # @task(1)
    # def login(self):
    #     self.client.post(
    #         '/login', {"deviceid": self._deviceid})

    @task(1000)
    def getBlogsMainPageData(self):

        self.client.get("/api/v1/post",
            auth=None,
            headers={"Accept": "application/json", "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest", "firebase-uid-for-testing": "abcdefghijklmnopqrstuvwxyz03"},
            name="Get Blog Home Page")

    @task(1000)
    def getBlogsNextPageData(self):

        self.client.get("/api/v1/post?cursor=eyJjcmVhdGVkX2F0IjoiMjAyMi0wNi0yMiAxMDozOTo0MiIsIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0",
            auth=None,
            headers={"Accept": "application/json", "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest", "firebase-uid-for-testing": "abcdefghijklmnopqrstuvwxyz03"},
            name="Get Blog Next Page")

    @task(1)
    def createUser(self):

        self.client.post("/api/v1/auth/create",
            data=json.dumps({}),
            auth=None,
            headers={"Accept": "application/json", "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest", "firebase-uid-for-testing": ''.join(random.choices(string.ascii_uppercase + string.digits, k = 28))},
            name="Create User")

    # @task(1)
    # def post_metrics(self):
    #     self.client.post(
    #         "/metrics", {"deviceid": self._deviceid, "timestamp": datetime.now()})


class MetricsLocust(FastHttpUser):
    tasks = {MetricsTaskSet}

# [END locust_test_task]
