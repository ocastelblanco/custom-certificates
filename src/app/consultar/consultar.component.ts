import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { SesionService, User } from 'src/app/servicios/sesion.service';

@Component({
  selector: 'app-consultar',
  templateUrl: './consultar.component.html',
  styleUrls: ['./consultar.component.scss']
})
export class ConsultarComponent implements OnInit {
  sesionActiva!: User | null;
  constructor(private sesion: SesionService, private route: Router) { }
  ngOnInit(): void {
    /* */
    this.sesion.login('ocastelblanco', '4q#ReusgBt8Quv&');
    //this.sesion.login('1018454250', 'afigueroar');
    /* */
    this.sesion.sesion().subscribe(s => {
      this.sesionActiva = s;
      this.sesionActiva ? this.route.navigateByUrl('/consultar/descargar') : null;
    });
  }
  logout() {
    this.sesion.logout();
  }
}
