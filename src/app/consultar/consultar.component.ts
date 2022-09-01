import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { SesionService, User } from 'src/app/servicios/sesion.service';
import { ApiService, Curso } from '../servicios/api.service';

@Component({
  selector: 'app-consultar',
  templateUrl: './consultar.component.html',
  styleUrls: ['./consultar.component.scss']
})
export class ConsultarComponent implements OnInit {
  sesionActiva!: User | null;
  constructor(private sesion: SesionService, private route: Router, private api: ApiService) { }
  ngOnInit(): void {
    this.api.init().subscribe((cursos: Curso[]) => {
      this.api.cursos = cursos;
      this.sesion.sesion().subscribe(s => {
        this.sesionActiva = s;
        //*
        this.sesionActiva = {
          id: '1',
          idnumber: '1',
          firstname: 'Oliver',
          lastname: 'Castelblanco',
          email: 'ocastelblanco@gmail.com',
          city: 'Bogotá',
          country: 'CO',
          institution: 'Mediateca',
          admin: true,
          sesionid: '1'
        };
        this.route.navigateByUrl('/consultar/utilidades');
        //*/
        if (!this.sesionActiva) this.route.navigateByUrl('/');
      });
    });
  }
  logout() {
    this.sesion.logout();
    window.close();
  }
}
