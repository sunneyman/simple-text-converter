(function(factory) {
  'use strict';
  if (typeof define === 'function' && define.amd) {
    define(['jquery'], factory);
  } else if (typeof exports !== 'undefined') {
    module.exports = factory(require('jquery'));
  } else {
    factory(jQuery);
  }

}(function($) {

  class SimpleTextConverter {
    constructor({ id }) {
      this.el = document.getElementById(id);

      this.cache = '';

      if (this.el) {
        this.mode = this.el.dataset.mode;

        this._initEvents();
        this._renderLayout( this.el.innerHTML );

        // immediately convert if it's readonly mode
        if ( this.mode === 'readonly' ) {
          this.convert();
        } else if ( this.el.dataset.postId ) {
          this.invert();
        }
      }
    }

    _initEvents() {
      this.el.addEventListener('click', this._onClick.bind(this));
      document.body.addEventListener('click', this._onClickGlobal.bind(this));
    }

    _onClick(e) {
      const dataAttr      = 'action';
      const buttonEl      = e.target.dataset[dataAttr] && e.target || e.target.closest('[data-action]');
      const dataAttrValue = buttonEl ? buttonEl.dataset[dataAttr] : null;

      if (dataAttrValue && typeof this[dataAttrValue] !== 'undefined')
        this[dataAttrValue].call(this, e, buttonEl);
    }

    _onClickGlobal(e) {
      const dataAttr      = 'stcAction';
      const buttonEl      = e.target.dataset[dataAttr] && e.target || e.target.closest('[data-stc-action]');
      const dataAttrValue = buttonEl ? buttonEl.dataset[dataAttr] : null;

      if (dataAttrValue && typeof this[dataAttrValue] !== 'undefined')
        this[dataAttrValue].call(this, e, buttonEl);
    }

    _renderLayout(content = '') {
      const panelButtons = `
        <div class="stc--panel stc--panel__textarea">
          <button class="button button_secondary button_icon" data-action="wrapSelectedByBold"><i class="fas fa-bold"></i></button>
          <button class="button button_secondary button_icon" data-action="insertImage"><i class="far fa-image"></i></button>
        </div>
      `;

      this.el.classList.add('stc');

      this.el.innerHTML = `
        <div class="stc--box">
          <div class="stc--area stc--area__input">
            <div class="stc--element">
              
              <div class="stc--panel stc--panel__image" hidden>
                <button 
                  class="button button_secondary button_icon" 
                  data-action="addIsActiveClass" 
                  data-stc="alignleft">
                    <i class="fas fa-align-left"></i>
                </button>
                
                <button 
                  class="button button_secondary button_icon" 
                  data-action="addIsActiveClass" 
                  data-stc="aligncenter">
                    <i class="fas fa-align-center"></i>
                </button>
                
                <button 
                  class="button button_secondary button_icon" 
                  data-action="addIsActiveClass" 
                  data-stc="alignright">
                    <i class="fas fa-align-right"></i>
                </button>
              </div>
            </div>

            <div class="stc--element">
              <textarea
                  placeholder="Type or copy/paste in your text here and click the Convert button…"
                  name="stc"
                  id="stc"
                  cols="30"
                  rows="10"
                  class="stc--element__textarea">${content}</textarea>
            </div>
          </div>
        </div>
      `;
    }

    convert(e, el) {
      if (el) {
        el.dataset.stcAction = 'backToTextarea';
        el.innerText = 'Back to Editor';
      }

      SimpleTextConverter.toggleSaveButton();

      this._convertTextarea();
    }

    invert() {

     this.convert(null, document.querySelector( '[data-stc-action="convert"]' ));

    }

    static toggleSaveButton() {

      const header = document.querySelector('.header');

      if (header) {
        header.querySelector('.header--save-panel').classList.toggle('hidden');
        header.classList.toggle('is-save-panel');
      }

    }

    save(e) {

      var button = e.target;
      var title  = document.querySelector('[name="post_title"]').value;
      var isPublic = document.querySelector('[name="post_public"]').checked;
      var data = {
        'action': 'save_sta_document',
        'document': this.cache,
        'post_title': title,
        'post_status': isPublic ? 'publish' : 'private',
        'post_id': this.el.dataset.postId || false,
      };

      $.ajax({
        'method': 'post',
        'url': window.sta.ajax,
        'data': data,
        'beforeSend': function() {
          button.setAttribute('disabled', 'disabled');
        },
        'complete': function (resp) {
          var response = JSON.parse(resp.responseText);

          if ( response.error ) {
            alert(response.error);
          } else if ( response.redirect ) {
            window.location.href = response.redirect;
          }

          button.removeAttribute('disabled');
        }
      });
    }

    backToTextarea(e, el) {
      el.dataset.stcAction = 'convert';
      el.innerText = 'Convert';

      SimpleTextConverter.toggleSaveButton();

      this._renderLayout(this.cache);
    }

    showOutputTab(e, el) {
      this.el.querySelector('.stc--element__output--text').hidden = false;
      this.el.querySelector('.stc--element__output--code').hidden = true;
      this.addIsActiveClass(e, el);
    }

    showCodeTab(e, el) {
      this.el.querySelector('.stc--element__output--code').hidden = false;
      this.el.querySelector('.stc--element__output--text').hidden = true;
      this.addIsActiveClass(e, el);
    }

    _convertTextarea() {
      const boxEl      = this.el.querySelector('.stc--box');
      const textAreaEl = this.el.querySelector('.stc--element__textarea');
      const output     = this.handleHTML(textAreaEl.value);
      this.cache       = textAreaEl.value;

      const panelButtons = `
          <div class="stc--element">
            <div class="stc--panel">
              <button class="button button_secondary button_icon is-active" data-action="showOutputTab"><i class="fas fa-font"></i></button>
              <button class="button button_secondary button_icon" data-action="showCodeTab"><i class="fas fa-code"></i></button>
            </div>
          </div>
      `;

      boxEl.innerHTML = `
        <div class="stc--area stc--area__output">
          <div class="stc--element stc--element__output stc--element__output--text"> 
            ${output}
          </div>

          <div class="stc--element stc--element__output stc--element__output--code" hidden>
            <textarea
                name="stc-html-output"
                id="stc"
                cols="30"
                rows="10"
                class="stc--element__textarea"
                readonly></textarea>
          </div>
        </div>
      `;

      const $outputText = $('.stc--element__output--text');
      $outputText
        .find('p, h1, h2, h3, h4, h5, h6').each(function(){
          $(this).splitLines({
            tag: '<span class="switcher-line">',
            keepHtml: true,
            width: $outputText.width()
          });
        });

      $outputText.find('p').each(function(){
        $(this).find('.switcher-line').each(function(index){
          [].forEach.call(this.childNodes, node => {
            if (node.textContent === ' '
              && (
                node.previousElementSibling !== null
                && node.previousElementSibling.nodeName === 'BR'
                && node.previousElementSibling.previousSibling === null
              )
            ) {
              $(node.parentNode).replaceWith($('<br>'));
            }
          });
        });
      });

      $('[name="stc-html-output"]').html($outputText[0].innerHTML);
    }

    wrapSelectedByBold() {
      const textAreaEl = this.el.querySelector('.stc--element__textarea');
      $(textAreaEl).surroundSelectedText('[bold]', '[/bold]');
    }

    wrapSelectedByP() {
      const textAreaEl = this.el.querySelector('.stc--element__textarea');
      $(textAreaEl).surroundSelectedText('<p>', '</p>');
    }

    getSelection() {
      const textAreaEl = this.el.querySelector('.stc--element__textarea');
      return $(textAreaEl).getSelection();
    }

    insertImage() {
      const textAreaEl   = this.el.querySelector('.stc--element__textarea');
      const panelTextEl  = this.el.querySelector('.stc--panel__textarea');
      const panelImageEl = this.el.querySelector('.stc--panel__image');
      const textParentEl = textAreaEl.parentElement;
      const insertPosition = this.getSelection().start;

      this.showImageUrlInput({
        onInit: () => {
          textParentEl.hidden = true;
          panelTextEl.hidden = true;
          panelImageEl.hidden = false;
          textParentEl.insertAdjacentHTML('afterEnd', `
            <div class="stc--element stc--element__input-url">
              <input type="url" name="stc-url" class="stc--element__input" placeholder="https://www.path.to/image.jpg">
              <div class="stc--element__buttons">
                <button class="button button_primary button_icon"><i class="fas fa-check"></i></button>
                <button class="button button_secondary button_icon"><i class="fas fa-times"></i></button>
              </div>
            </div>
          `);
        },
        onInsert: (url, className) => {
          $(textAreaEl).insertText(
            `<img src="${url}" class="${className}" alt='' />`,
            insertPosition,
            'collapseToEnd'
          );
        },
        onClose: () => {
          textParentEl.hidden = false;
          panelTextEl.hidden = false;
          panelImageEl.hidden = true;
          this.el.querySelector('.stc--element__input-url').remove();
        }
      });

    }

    showImageUrlInput({onInit, onInsert, onClose}) {
      onInit();

      const urlInputEl = this.el.querySelector('.stc--element__input-url');

      urlInputEl && urlInputEl.addEventListener('click', function(e) {
        const target = e.target.classList.contains('fas') ? e.target : e.target.children[0];

        if (typeof target !== 'undefined' && target.classList.contains('fas')) {
          if (target.classList.contains('fa-check')) {
            const url     = urlInputEl.querySelector('[name="stc-url"]').value;
            const panelEl = document.querySelector('.stc--panel__image');
            const className = panelEl.querySelector('.is-active') ? panelEl.querySelector('.is-active').dataset.stc : 'alignnone';

            if (url, className)
              onInsert(url, className);
          }

          onClose();
        }
      });
    }

    addIsActiveClass(e, el) {
      [].forEach.call(el.parentNode.children, currentEl => currentEl.classList.remove('is-active'));
      el.classList.add('is-active');
    }

    handleHTML(value) {
      const $element = $(`<div>${value}</div>`);

      [].forEach.call($element[0].childNodes, function(element){
        if (element.nodeName === '#text' && element.textContent.match(/[a-z]/i)) {
          $(element).wrap('<p></p>');
        }
      });

      $element[0].innerHTML = $element[0].innerHTML.replace(/\[bold\]/g, '<b>').replace(/\[\/bold\]/g, '</b>');
      $element.find('p, h1, h2, h3, h4, h5, h6, b').each(function () {
        this.innerHTML = this.innerHTML.replace(/\r?\n/g,'<br/>');
      });

      $element[0].innerHTML = $element[0].innerHTML.replace(/<p><br>/g, '<p>');
      $element[0].innerHTML = $element[0].innerHTML.replace(/<br><\/p>/g, '</p>');

      return $element[0].innerHTML;
    }
  }

  window.SimpleTextConverter = SimpleTextConverter;

}));