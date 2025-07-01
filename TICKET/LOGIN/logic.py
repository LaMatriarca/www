
def login(usuario, contrasena):
    # Usuario y contraseña hardcodeados para prueba
    if usuario == "admin" and contrasena == "1234":
        return "Login correcto: Administrador"
    elif usuario == "usuario" and contrasena == "abcd":
        return "Login correcto: Usuario"
    else:
        return "Usuario o contraseña incorrectos"

if __name__ == "__main__":
    user = input("Usuario: ")
    pwd = input("Contraseña: ")
    resultado = login(user, pwd)
    print(resultado)
