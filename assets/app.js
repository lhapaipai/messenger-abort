/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

window.addEventListener("DOMContentLoaded", () => {
  let $status = document.querySelector("#status")
  document.querySelector("#start").addEventListener("click", () => {
    $status.innerText = "started"
  })
  document.querySelector("#abort").addEventListener("click", () => {
    $status.innerText = "aborted"
  })

})