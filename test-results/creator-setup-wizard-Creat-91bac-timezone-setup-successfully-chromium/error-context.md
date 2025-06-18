# Page snapshot

```yaml
- main:
  - link:
    - /url: http://localhost:8000
    - img
  - heading "Welcome back!" [level=1]
  - text: Email Address
  - textbox "Email Address": creator@example.com
  - text: Les identifiants fournis ne correspondent pas à nos enregistrements. Password
  - textbox "Password"
  - link "Forgot Password?":
    - /url: http://localhost:8000/forgot-password
  - button "Sign In"
  - text: Don't you have an account?
  - link "Sign Up":
    - /url: http://localhost:8000/choose-role
  - img
  - text: To support you during the pandemic super pro features are free until March 31st.
```