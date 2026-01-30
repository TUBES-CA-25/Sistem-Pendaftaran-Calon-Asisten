// Dependencies: common.js untuk showModal() dan showConfirm()


document.addEventListener("DOMContentLoaded", () => {
  const questions = document.querySelectorAll(
    ".questions-container > .question"
  );
  const navButtons = document.querySelectorAll(".nav button");
  const timerElement = document.getElementById("timer");
  const endpoint = APP_URL + "/hasil";
  let currentQuestion = 0;
  const initialDuration = 30 * 60;
  let remainingTime;

  const storage = window.storage || {
    get: (k) => { try { return localStorage.getItem(k); } catch(e) { return null; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch(e) {} },
    remove: (k) => { try { localStorage.removeItem(k); } catch(e) {} }
  };

  const answers = {};

  if (storage.get("remainingTime")) {
    remainingTime = parseInt(storage.get("remainingTime"), 10);

  } else {
    remainingTime = initialDuration;
  }

  async function submitAndFinish(priorResponse) {
    // Note: The score is already calculated in saveAnswer (/hasil)
    // We just need to show success and redirect.
    console.log("Submitting finish...", priorResponse);
    
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
                "/Sistem-Pendaftaran-Calon-Asisten/public/Assets/gif/glasshour.gif"
              );
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
          alert("DEBUG ERROR BACKEND:\n" + xhr.responseText); // Force user to see error
          reject(new Error("Error saat menyimpan jawaban: " + status));
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
        showActionConfirmation({
            title: 'Selesaikan Ujian',
            message: 'Apakah Anda yakin ingin menyelesaikan ujian ini?<br><span class="text-muted small">Jawaban yang sudah dikirim tidak dapat diubah kembali.</span>',
            btnText: 'Selesai',
            type: 'success', // Green for positive submission
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
  }

  navButtons.forEach((button, index) => {
    button.addEventListener("click", () => {
      currentQuestion = index;
      showQuestion(currentQuestion);
    });
  });

  questions.forEach((question, index) => {
    const options = question.querySelectorAll("input[type='radio']");
    const textarea = question.querySelector("textarea.text-answer");
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
        markAnsweredQuestion(index);
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
