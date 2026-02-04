const show_pw_btn = document.querySelectorAll('.show-passwd')
const pw_input = document.querySelectorAll('.pass')

show_pw_btn.forEach((button, index) => {
  const icon = button.querySelector('img')
  const input = pw_input[index];

  button.addEventListener('click', () => {
    input.type = input.type === 'password' ? 'text' : 'password';

    icon.src = icon.src.includes('closed') 
        ? '../resources/images/eye_open.svg' 
        : '../resources/images/eye_closed.svg';
    });
});