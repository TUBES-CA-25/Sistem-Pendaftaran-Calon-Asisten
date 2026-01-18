// Dependencies: common.js untuk showModal() dan showConfirm()


document.addEventListener("DOMContentLoaded", () => {
  const questions = document.querySelectorAll(
    ".questions-container > .question"
  );
  const navButtons = document.querySelectorAll(".nav button");
  const timerElement = document.getElementById("timer");
  const endpoint = `${APP_URL}/hasil`;
  let currentQuestion = 0;
  const initialDuration = 30 * 60;
  let remainingTime;

  const answers = {};

  if (localStorage.getItem("remainingTime")) {
    remainingTime = parseInt(localStorage.getItem("remainingTime"), 10);
  } else {
    remainingTime = initialDuration;
  }

  async function submitAndFinish() {
    const calculateEndpoint = `${APP_URL}/calculate`;

    try {
      const response = await fetch(calculateEndpoint, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error: ${response.status}`);
      }

      const textResponse = await response.text();
      try {
        const data = JSON.parse(textResponse);
        console.log("Respons dari backend:", data);

        if (data.status === "success") {
          setTimeout(() => {
            window.location.href = `${APP_URL}`;
          }, 3000);
        } else {
          setTimeout(() => {
            window.location.href = `${APP_URL}`;
          }, 3000);
        }
      } catch (parseError) {
        setTimeout(() => {
          window.location.href = `${APP_URL}`;
        }, 3000);
      }
    } catch (error) {
      setTimeout(() => {
        window.location.href = `${APP_URL}`;
      }, 3000);
    }
  }

  function updateTimerDisplay(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    timerElement.textContent = `${String(minutes).padStart(2, "0")}:${String(
      remainingSeconds
    ).padStart(2, "0")}`;
  }

  function startCountdown() {
    const countdown = setInterval(() => {
      if (remainingTime <= 0) {
        clearInterval(countdown);
        showModal(
          "Waktu Habis jawaban akan di kumpul",
          `${APP_URL}/Assets/gif/glasshour.gif`
        );
        submitAllAnswers()
          .then(() => submitAndFinish())
          .catch((error) => {
            console.error("Error saat menyimpan jawaban:", error);
            showModal;
          });
      } else {
        remainingTime--;
        updateTimerDisplay(remainingTime);
        localStorage.setItem("remainingTime", remainingTime);
      }
    }, 1000);
  }

  function showQuestion(index) {
    questions.forEach((question, i) => {
      question.style.display = i === index ? "block" : "none";
    });
    updateNavigationButtons();
    updateActiveNavButton();
  }
  function submitAllAnswers() {
    const data = Object.entries(answers).map(([idSoal, jawaban]) => ({
      id_soal: idSoal,
      jawaban: jawaban,
    }));

    return new Promise((resolve, reject) => {
      $.ajax({
        url: endpoint,
        type: "POST",
        contentType: "application/json",
        data: JSON.stringify(data),
        success: function (response) {
          try {
            if (typeof response !== "object" || !response.status) {
              throw new Error("Format respons tidak valid");
            }

            if (response.status === "success") {
              showModal(
                "Jawaban berhasil disimpan. Silahkan menunggu pengumuman selanjutnya",
                `${APP_URL}/Assets/gif/glasshour.gif`
              );
              localStorage.removeItem("remainingTime");
              localStorage.removeItem("examAnswers");
              resolve(response);
            } else {
              console.error(
                "Error :",
                response.message || "Tidak ada pesan error"
              );
              reject(
                new Error(
                  `Backend error: ${
                    response.message || "Tidak ada pesan error"
                  }`
                )
              );
            }
          } catch (error) {
            console.error("Kesalahan saat memproses respons backend:", error);
            reject(error);
          }
        },
        error: function (xhr, status, error) {
          console.error("Error saat menyimpan jawaban:", error);
          console.log("Respons backend:", xhr.responseText);
          reject(new Error("Error saat menyimpan jawaban."));
        },
      });
    });
  }

  function updateNavigationButtons() {
    let backButton =
      questions[currentQuestion].querySelector(".nav-button.back");
    let nextButton =
      questions[currentQuestion].querySelector(".nav-button.next");

    if (!backButton) {
      backButton = document.createElement("button");
      backButton.classList.add("nav-button", "back");
      backButton.textContent = "Back";
      backButton.style.float = "left";
      questions[currentQuestion].appendChild(backButton);
    }

    if (!nextButton) {
      nextButton = document.createElement("button");
      nextButton.classList.add("nav-button", "next");
      nextButton.textContent =
        currentQuestion === questions.length - 1 ? "Finish" : "Next";
      nextButton.style.float = "right";
      questions[currentQuestion].appendChild(nextButton);
    }

    backButton.style.display = currentQuestion === 0 ? "none" : "inline-block";
    nextButton.textContent =
      currentQuestion === questions.length - 1 ? "Finish" : "Next";

    backButton.onclick = () => {
      if (currentQuestion > 0) {
        currentQuestion--;
        showQuestion(currentQuestion);
      }
    };

    nextButton.onclick = () => {
      if (currentQuestion < questions.length - 1) {
        currentQuestion++;
        showQuestion(currentQuestion);
      } else {
        showConfirm("Apakah Anda yakin ingin menyelesaikan ujian?", () => {
          submitAllAnswers()
            .then(() => {
              return submitAndFinish();
            })
            .catch((error) => {
              console.error(
                "Error saat menyimpan jawaban atau menghitung nilai:",
                error
              );
            });
        });
      }
    };
  }

  function updateActiveNavButton() {
    navButtons.forEach((button) => button.classList.remove("active"));
    if (navButtons[currentQuestion]) {
      navButtons[currentQuestion].classList.add("active");
    }
  }

  function markAnsweredQuestion(index) {
    if (navButtons[index]) {
      navButtons[index].classList.add("answered");
    }
  }

  function saveAnswer(idSoal, answer) {
    answers[idSoal] = answer;
    localStorage.setItem("examAnswers", JSON.stringify(answers));
  }

  navButtons.forEach((button, index) => {
    button.addEventListener("click", () => {
      currentQuestion = index;
      showQuestion(currentQuestion);
    });
  });

  // Load answers from localStorage
  const savedAnswers = JSON.parse(localStorage.getItem("examAnswers")) || {};
  Object.assign(answers, savedAnswers);

  questions.forEach((question, index) => {
    const options = question.querySelectorAll("input[type='radio']");
    const textarea = question.querySelector("textarea.text-answer");
    const idSoal = question.getAttribute("data-id-soal");

    // Restore state from saved answers
    if (answers[idSoal]) {
      if (textarea) {
        textarea.value = answers[idSoal];
      } else {
        options.forEach((option) => {
          if (option.value === answers[idSoal]) {
            option.checked = true;
          }
        });
      }
      markAnsweredQuestion(index);
    }

    options.forEach((option) => {
      option.addEventListener("change", () => {
        const answer = option.value;
        saveAnswer(idSoal, answer);
        markAnsweredQuestion(index);
      });
    });

    if (textarea) {
      textarea.addEventListener("input", () => {
        const answer = textarea.value.trim();
        saveAnswer(idSoal, answer);
        console.log(
          `Jawaban disimpan: { idSoal: ${idSoal}, answer: "${answer}" }`
        );
        markAnsweredQuestion(index);
      });
    }
  });

  showQuestion(currentQuestion);
  updateTimerDisplay(remainingTime);
  startCountdown();

  window.addEventListener("beforeunload", () => {
    if (remainingTime <= 0) {
      localStorage.removeItem("remainingTime");
      localStorage.removeItem("examAnswers");
    }
  });
});
