import { Component, OnInit } from '@angular/core';
import { ApiService, Curso } from 'src/app/servicios/api.service';
import { ExcelService } from 'src/app/servicios/excel.service';

@Component({
  selector: 'app-generador',
  templateUrl: './generador.component.html',
  styleUrls: ['./generador.component.scss']
})
export class GeneradorComponent implements OnInit {
  data: any[] = [];
  convirtiendo: boolean = false;
  archivoCargado: boolean = false;
  constructor(private api: ApiService, private excel: ExcelService) { }
  ngOnInit(): void {
  }
  sueltaArchivo(archivo: File): void {
    this.archivoCargado = true;
    this.convirtiendo = true;
    this.procesaExcel(archivo);
  }
  procesaExcel(archivo: File): void {
    this.excel.readExcelFile(archivo).subscribe(res => {
      if (res) {
        this.data = [];
        res.forEach((row: string[], index: number) => {
          if (index > 0 && row.length > 0 && row[0]) {
            this.data.push({
              firstname: this.nomPropio(row[0]),
              lastname: this.nomPropio(row[1]),
              email: row[4].toLowerCase(),
              institution: this.nomInstitucion(row[5]),
              city: this.nomPropio(row[6]),
              curso: this.nomCurso(row[8]),
              idnumber: this.limpiaID(row[9]),
            });
          }
        });
        this.convirtiendo = false;
      }
    });
  }
  nomPropio(nombre: string): string {
    return nombre.trim().replace(/[^\s]\S*/g, function (t) { return t.charAt(0).toUpperCase() + t.substring(1).toLowerCase(); });
  }
  limpiaID(id: string | number): string {
    return String(id).replace(/[^a-zA-Z0-9]/g, '');
  }
  nomInstitucion(nombre: string): string {
    return nombre.trim().indexOf(' ') > -1 ? this.nomPropio(nombre).trim() : nombre.trim();
  }
  nomCurso(nombre: string): string {
    nombre = nombre.trim().toLowerCase();
    let nomCurso: string = 'Nombre de curso no encontrado';
    this.api.cursos.forEach((curso: Curso) => {
      curso.claves.forEach((clave: string) => {
        if (nombre.indexOf(clave) > 1) nomCurso = curso.comun;
      });
    });
    return nomCurso;
  }
}
