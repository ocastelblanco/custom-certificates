import { Component } from '@angular/core';
import { ApiService } from 'src/app/servicios/api.service';
import { User } from '../servicios/sesion.service';

export interface Validacion extends User {
  fullname: string;
  intensidad: string;
  fecha: string;
}

@Component({
  selector: 'app-validar',
  templateUrl: './validar.component.html',
  styleUrls: ['./validar.component.scss']
})
export class ValidarComponent {
  id!: string;
  resultado: Validacion[] | null = null;
  alerta: boolean = false;
  constructor(private api: ApiService) { }
  valida() {
    this.api.list(this.id).subscribe(r => r.length > 0 ? this.resultado = r : this.alerta = true);
  }
  fecha(s: string): number {
    return parseInt(s) * 1000;
  }
  esNumero(s: string): boolean {
    return s.match(/\d/g)?.length === s.length;
  }
  reinicia(): void {
    this.resultado = null;
    this.id = '';
  }
}
