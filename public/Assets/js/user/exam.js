// Dependencies: common.js untuk showModal() dan showConfirm()


document.addEventListener("DOMContentLoaded", () => {
  const questions = document.querySelectorAll(
    ".questions-container > .question"
  );
  const navButtons = document.querySelectorAll(".nav-btn-soal");
  const questionNumberEl = document.getElementById("current-question-number");
  const timerElement = document.getElementById("timer");
  const endpoint = APP_URL + "/hasil";
  let currentQuestion = 0;
  const initialDuration = (window.examDuration || 45) * 60;
  let remainingTime;

  const storage = window.storage || {
    get: (k) => { try { return localStorage.getItem(k); } catch(e) { return null; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch(e) {} },
    remove: (k) => { try { localStorage.removeItem(k); } catch(e) {} }
  };

  const answers = {};

  // Get current exam session ID from the page (set by server)
  const currentExamSession = window.examSessionId || Date.now().toString();
  const storedExamSession = storage.get("examSessionId");

  // If this is a new exam session (different ID = reset by admin or first time), reset timer
  if (storedExamSession !== currentExamSession) {
    storage.remove("remainingTime");
    storage.set("examSessionId", currentExamSession);
    remainingTime = initialDuration;
  } else if (storage.get("remainingTime")) {
    remainingTime = parseInt(storage.get("remainingTime"), 10);
  } else {
    remainingTime = initialDuration;
  }

  async function submitAndFinish(priorResponse) {
    // Note: The score is already calculated in saveAnswer (/hasil)
    // We just need to show success and redirect.
    console.log("Submitting finish...", priorResponse);

    // Clear timer from storage since exam is completed
    storage.remove("remainingTime");
    storage.remove("examSessionId");

    setTimeout(() => {
        window.location.href = APP_URL + "/tes-tulis";
    }, 2000);
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
          "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/glasshour.gif"
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
        storage.set("remainingTime", remainingTime);
      }
    }, 1000);
  }

  function showQuestion(index) {
    questions.forEach((question, i) => {
      question.style.display = i === index ? "block" : "none";
    });
    // Update question number indicator
    if (questionNumberEl) {
      questionNumberEl.textContent = index + 1;
    }
    updateNavigationButtons();
    updateActiveNavButton();
  }
  function submitAllAnswers() {
    const data = Object.entries(answers).map(([idSoal, jawaban]) => ({
      id_soal: idSoal,
      jawaban: jawaban,
    }));

    // Vanilla fetch (dulu $.ajax — padahal soal.php tidak pernah memuat jQuery,
    // sehingga pengiriman jawaban melempar "$ is not defined").
    return fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then(function (res) {
        return res.text().then(function (raw) {
          let response;
          try {
            response = JSON.parse(raw);
          } catch (e) {
            console.error("Respons backend bukan JSON:", raw);
            throw new Error("Format respons tidak valid");
          }

          if (typeof response !== "object" || !response.status) {
            throw new Error("Format respons tidak valid");
          }

          if (response.status === "success") {
            showModal(
              "Jawaban berhasil disimpan. Silahkan menunggu pengumuman selanjutnya",
              "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/glasshour.gif"
            );
            return response;
          }

          console.error("Error :", response.message || "Tidak ada pesan error");
          throw new Error(
            `Backend error: ${response.message || "Tidak ada pesan error"}`
          );
        });
      })
      .catch(function (error) {
        console.error("Error saat menyimpan jawaban:", error);
        throw error;
      });
  }

  function updateNavigationButtons() {
    let backButton =
      questions[currentQuestion].querySelector(".back-btn");
    let nextButton =
      questions[currentQuestion].querySelector(".next-btn");
    let finishButton =
      questions[currentQuestion].querySelector(".finish-btn");

    if (!backButton) {
      console.error("Back button not found");
      return;
    }

    if (!nextButton) {
      console.error("Next button not found");
      return;
    }

    if (!finishButton) {
      console.error("Finish button not found");
      return;
    }

    // Show/hide buttons based on current question
    backButton.style.display = currentQuestion === 0 ? "none" : "inline-block";

    if (currentQuestion === questions.length - 1) {
      nextButton.style.display = "none";
      finishButton.style.display = "inline-block";
    } else {
      nextButton.style.display = "inline-block";
      finishButton.style.display = "none";
    }

    // Back button handler
    backButton.onclick = () => {
      if (currentQuestion > 0) {
        currentQuestion--;
        showQuestion(currentQuestion);
      }
    };

    // Next button handler
    nextButton.onclick = () => {
      if (currentQuestion < questions.length - 1) {
        currentQuestion++;
        showQuestion(currentQuestion);
      }
    };

    // Finish button handler
    finishButton.onclick = () => {
      showActionConfirmation({
        message: 'Apakah Anda yakin ingin menyelesaikan ujian ini? Jawaban yang sudah dikirim tidak dapat diubah kembali.',
        onConfirm: function() {
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
        }
      });
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
  }

  navButtons.forEach((button, index) => {
    button.addEventListener("click", () => {
      currentQuestion = index;
      showQuestion(currentQuestion);
    });
  });

  questions.forEach((question, index) => {
    const options = question.querySelectorAll("input[type='radio']");
    const textarea = question.querySelector("textarea.form-control");
    const idSoal = question.getAttribute("data-id-soal");

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
        // Only mark as answered if there's actual content
        if (answer.length > 0) {
          markAnsweredQuestion(index);
        } else {
          // Remove answered mark if textarea is cleared
          if (navButtons[index]) {
            navButtons[index].classList.remove("answered");
          }
        }
      });
    }
  });

  showQuestion(currentQuestion);
  updateTimerDisplay(remainingTime);
  startCountdown();

  window.addEventListener("beforeunload", () => {
    if (remainingTime <= 0) {
      storage.remove("remainingTime");
    }
  });
});
