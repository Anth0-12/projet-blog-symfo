// any CSS you import will output into a single css file (app.css in this case)
import "../css/app.scss";

import { Dropdown } from "bootstrap";

document.addEventListener("DOMContentLoaded", () => {
  new App();
});

class App {
  constructor() {
    this.enableDropdowns();
    this.handleCommentform();
  }

  enableDropdowns() {
    const dropdownElementList = document.querySelectorAll(".dropdown-toggle");
    const dropdownList = [...dropdownElementList].map(
      (dropdownToggleEl) => new Dropdown(dropdownToggleEl)
    );
  }

  handleCommentform() {
    const commentForm = document.querySelector("form.comment-form");

    if (null === commentForm) {
      return;
    }

    commentForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const response = await fetch("/ajax/comments", {
        method: "POST",
        body: new FormData(e.target),
      });

      if (!response.ok) {
        return;
      }

      const json = await response.json();

      if (json.code === "COMMENT_ADDED_SUCCESSFULLY") {
        const commentList = document.querySelector(".comment-list");
        const commentCount = document.querySelector(".comment-count");
        const commentContent = document.querySelector("#comment_content");

        commentList.insertAdjacentHTML("afterbegin", json.message);
        commentCount.innerText = json.numberOfComments;
        commentContent.value = "";

        console.log(json.numberOfComments);

        if (json.numberOfComments > 5) {
          location.reload();
        }
      }
    });
  }
}
