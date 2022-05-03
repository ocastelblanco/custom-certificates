import { Component } from '@angular/core';
import { SesionService } from 'src/app/servicios/sesion.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent {
  user!: string;
  pass!: string;
  alerta: boolean = false;
  cargando: boolean = false;
  constructor(private sesion: SesionService) { }
  login(): void {
    this.cargando = true;
    this.sesion.login(this.user, this.pass).subscribe(r => {
      this.cargando = false;
      this.alerta = r ? false : true;
    });
  }
}
