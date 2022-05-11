import {
  Directive,
  EventEmitter,
  Output,
  HostListener,
  HostBinding,
} from '@angular/core';

@Directive({
  selector: '[appDragDropFile]'
})
export class DragDropFileDirective {
  @Output() fileDropped: EventEmitter<any> = new EventEmitter<any>();
  @HostBinding('class') private clase = 'estatico';
  @HostListener('dragover', ['$event']) dragOver(event: any) {
    event.preventDefault();
    event.stopPropagation();
    this.clase = 'over';
  }
  @HostListener('dragleave', ['$event']) public dragLeave(event: any) {
    event.preventDefault();
    event.stopPropagation();
    this.clase = 'leave';
  }
  @HostListener('drop', ['$event']) public drop(event: any) {
    event.preventDefault();
    event.stopPropagation();
    this.clase = 'drop';
    const files = event.dataTransfer.files;
    if (files.length > 0) {
      const archivo: File = files[0];
      if (archivo.type.includes('text/csv')) this.fileDropped.emit(archivo);
    }
  }
}
