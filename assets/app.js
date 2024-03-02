/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';


class TaskObserver {
  constructor(elt, id) {
    this.elt = elt;
    this.id = parseInt(elt.dataset.id);
    this.status = elt.dataset.status;

    this.refreshTimeoutId = null;


    this.refresh = this.refresh.bind(this);
    this.abort = this.abort.bind(this);
    this.delete = this.delete.bind(this);

    this.elt.querySelector(".abort")?.addEventListener("click", this.abort);
    this.elt.querySelector(".delete")?.addEventListener("click", this.delete);

    if (this.status === "pending") {
      this.refresh();
    }

  }

  async abort() {
    clearTimeout(this.refreshTimeoutId)

    const res = await fetch(`/task/${this.id}/abort`);
    const task = await res.json()

    this.status = task.status;
    this.elt.querySelector(".status").innerText = this.status;

    this.elt.querySelector(".abort")?.remove();
  }

  async delete() {
    clearTimeout(this.refreshTimeoutId)
    const res = await fetch(`/task/${this.id}/delete`)
    console.log(res)
    if (res.status === 204) {
      this.elt.remove();
    }
  }

  async refresh() {
    const res = await fetch(`/task/${this.id}`);
    const task = await res.json()

    this.status = task.status;
    this.elt.querySelector(".status").innerText = task.status;
    this.elt.querySelector(".result").innerText = task.result;

    if (this.status === "pending") {
      this.refreshTimeoutId = setTimeout(this.refresh, 1000);
    } else {
      this.elt.querySelector(".abort")?.remove();
    }
  }
}


window.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll('.task').forEach($task => {
    new TaskObserver($task);
  })

  document.querySelector("#start").addEventListener("click", async () => {

    const res = await fetch("/task/create")
    const task = await res.json()

    let fragment = document.querySelector('#task-template').content.cloneNode(true);
    let $task = fragment.querySelector(".task")

    $task.querySelector(".id").innerText = task.id;
    $task.querySelector(".status").innerText = task.status;

    document.querySelector(".tasks").append(fragment);

    $task.dataset.id = task.id;
    $task.dataset.status = task.status;

    new TaskObserver($task);
  })

})