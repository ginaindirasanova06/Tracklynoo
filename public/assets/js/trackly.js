/**
 * ==========================================================================
 * TRACKLY - MODERN HEALTHCARE TECHNOLOGY CLIENT ENGINE
 * Features: Web Audio API Synth, Sound Toggle, Counter Animation, OTP Handler
 * ==========================================================================
 */

// 1. Web Audio API Sound Synthesizer (No external MP3 files needed)
const TracklyAudio = (() => {
  let audioCtx = null;

  function getAudioContext() {
    if (!audioCtx) {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      if (AudioContext) {
        audioCtx = new AudioContext();
      }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
      audioCtx.resume();
    }
    return audioCtx;
  }

  function isEnabled() {
    const val = localStorage.getItem('trackly_sound_enabled');
    return val === null ? true : val === 'true';
  }

  function setEnabled(enabled) {
    localStorage.setItem('trackly_sound_enabled', enabled ? 'true' : 'false');
    updateToggleButton();
  }

  function play(type = 'success') {
    if (!isEnabled()) return;
    try {
      const ctx = getAudioContext();
      if (!ctx) return;

      const now = ctx.currentTime;

      if (type === 'scan') {
        // Quick subtle blip for camera scan (E6 ~1318Hz, 70ms)
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(1318.5, now);
        gain.gain.setValueAtTime(0.08, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.07);
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.08);

      } else if (type === 'success') {
        // Cheerful medical chime (C5 523Hz -> G5 784Hz)
        [523.25, 783.99].forEach((freq, i) => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          const start = now + (i * 0.09);
          osc.type = 'sine';
          osc.frequency.setValueAtTime(freq, start);
          gain.gain.setValueAtTime(0.09, start);
          gain.gain.exponentialRampToValueAtTime(0.001, start + 0.22);
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start(start);
          osc.stop(start + 0.24);
        });

      } else if (type === 'error') {
        // Soft double warning note (Db4 277Hz -> A3 220Hz)
        [277.18, 220.0].forEach((freq, i) => {
          const osc = ctx.createOscillator();
          const gain = ctx.createGain();
          const start = now + (i * 0.1);
          osc.type = 'triangle';
          osc.frequency.setValueAtTime(freq, start);
          gain.gain.setValueAtTime(0.1, start);
          gain.gain.exponentialRampToValueAtTime(0.001, start + 0.16);
          osc.connect(gain);
          gain.connect(ctx.destination);
          osc.start(start);
          osc.stop(start + 0.18);
        });
      }
    } catch (e) {
      console.warn('TracklyAudio notice:', e);
    }
  }

  function updateToggleButton() {
    const btn = document.getElementById('sound-toggle-btn');
    if (!btn) return;
    const enabled = isEnabled();
    btn.classList.toggle('muted', !enabled);
    btn.title = enabled ? 'Suara Aktif (Klik untuk Mematikan)' : 'Suara Dimatikan (Klik untuk Mengaktifkan)';
    btn.innerHTML = enabled
      ? `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg><span style="font-size:0.75rem;">Suara Aktif</span>`
      : `<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg><span style="font-size:0.75rem;">Hening</span>`;
  }

  return { play, isEnabled, setEnabled, updateToggleButton };
})();

// Expose globally
window.TracklyAudio = TracklyAudio;

// 2. Animated Number Counter (Lightweight CountUp)
function initAnimatedCounters() {
  const elements = document.querySelectorAll('.stat-value');
  elements.forEach((el) => {
    // Check if element has pure numeric text (possibly with commas)
    const rawText = el.textContent.trim().replace(/,/g, '');
    const target = parseFloat(rawText);
    if (!isNaN(target) && target > 0 && target <= 1000000) {
      el.textContent = '0';
      const duration = 800; // ms
      const startTime = performance.now();

      function updateNumber(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // EaseOutExpo
        const current = progress === 1 ? target : Math.round(target * (1 - Math.pow(2, -10 * progress)));
        el.textContent = current.toLocaleString('id-ID');
        if (progress < 1) {
          requestAnimationFrame(updateNumber);
        }
      }
      requestAnimationFrame(updateNumber);
    }
  });
}

// 3. Document Ready Engine
document.addEventListener('DOMContentLoaded', () => {
  // Initialize Sound Toggle UI
  TracklyAudio.updateToggleButton();
  const soundBtn = document.getElementById('sound-toggle-btn');
  if (soundBtn) {
    soundBtn.addEventListener('click', () => {
      const nextState = !TracklyAudio.isEnabled();
      TracklyAudio.setEnabled(nextState);
      if (nextState) {
        TracklyAudio.play('success');
      }
    });
  }

  // Play audio on server flash alert presence
  const hasSuccessAlert = document.querySelector('.alert-success');
  const hasErrorAlert = document.querySelector('.alert-error');
  if (hasSuccessAlert) TracklyAudio.play('success');
  if (hasErrorAlert) TracklyAudio.play('error');

  // Trigger counters on dashboard
  initAnimatedCounters();

  // OTP 6-Digit Inputs Auto-Focus & Paste Handling
  const otpInputs = document.querySelectorAll('.otp-box');
  if (otpInputs.length === 6) {
    otpInputs[0].focus();

    otpInputs.forEach((input, index) => {
      input.addEventListener('input', (e) => {
        const val = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = val ? val[val.length - 1] : '';

        if (e.target.value && index < 5) {
          otpInputs[index + 1].focus();
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !input.value && index > 0) {
          otpInputs[index - 1].focus();
        }
      });

      input.addEventListener('paste', (e) => {
        e.preventDefault();
        const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
        const digits = pasteData.replace(/[^0-9]/g, '').slice(0, 6);

        for (let i = 0; i < digits.length; i++) {
          if (otpInputs[i]) {
            otpInputs[i].value = digits[i];
          }
        }

        const nextIndex = Math.min(digits.length, 5);
        otpInputs[nextIndex].focus();
      });
    });
  }

  // Countdown Timer for OTP (5 minutes)
  const timerElem = document.getElementById('otp-timer');
  if (timerElem) {
    let timeLeft = 300;
    const interval = setInterval(() => {
      timeLeft--;
      if (timeLeft <= 0) {
        clearInterval(interval);
        timerElem.innerHTML = '<span style="color:#f43f5e; font-weight:700;">Kedaluwarsa</span>';
      } else {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerElem.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
      }
    }, 1000);
  }

  // Resend OTP Cooldown (60s)
  const resendBtn = document.getElementById('btn-resend-otp');
  const resendTimer = document.getElementById('resend-countdown');
  if (resendBtn && resendTimer) {
    let waitSeconds = 60;
    const cooldownInterval = setInterval(() => {
      waitSeconds--;
      if (waitSeconds <= 0) {
        clearInterval(cooldownInterval);
        resendTimer.textContent = '';
        resendBtn.removeAttribute('disabled');
        resendBtn.style.opacity = '1';
        resendBtn.style.pointerEvents = 'auto';
      } else {
        resendTimer.textContent = ` (${waitSeconds}s)`;
      }
    }, 1000);
  }

  // Mobile Sidebar Toggle
  const sidebarToggle = document.getElementById('sidebar-toggle');
  const sidebar = document.getElementById('sidebar');
  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });
  }
});
