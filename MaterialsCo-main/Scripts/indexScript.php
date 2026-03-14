<script>

  //Functionality for Panels of Signup and Signin 
    const showSignUp = document.getElementById('showSignUp');
    const showSignIn = document.getElementById('showSignIn');
    const SignInForm = document.getElementById('SignInForm');
    const SignUpForm = document.getElementById('SignUpForm');

    showSignUp.addEventListener('click', () => {
        SignInForm.classList.add('hidden');
        SignUpForm.classList.remove('hidden');
    });

    showSignIn.addEventListener('click', () => {
        SignUpForm.classList.add('hidden');
        SignInForm.classList.remove('hidden');
    });


</script>
