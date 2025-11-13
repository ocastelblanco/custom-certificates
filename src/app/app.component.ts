import { Component, OnInit } from '@angular/core';
import { PdfService } from './servicios/pdf.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent implements OnInit {
  constructor(private pdfService: PdfService) { }
  ngOnInit(): void {
    this.pdfService.init();
  }
}
