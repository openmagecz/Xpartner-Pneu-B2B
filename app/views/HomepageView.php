<!DOCTYPE html>
<html class="no-js" lang="cs">

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta charset="utf-8">
    <link rel="preconnect" href="https://use.typekit.net" crossorigin>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?php if (empty($pneux)) {
            echo "Katalog pneumatik skladem";
        } else {
            echo "Výsledky vyhledávání pneumatik skladem";
        } ?>
    </title>
    <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="../ui/css/main.css?v=19" />
    <script>
        function loadDesktopCSS() { if (!desktopStylesLoaded && window.innerWidth >= 600) { for (var e = 0, d = document.querySelectorAll('head > link[href*="css"][media="screen and (min-width:37.5em)"]'); e < d.length; e++)d[e].removeAttribute("disabled"); desktopStylesLoaded = !0 } } var desktopStylesLoaded = !1; loadDesktopCSS(), window.addEventListener("resize", loadDesktopCSS)
    </script>
    <link rel="stylesheet" href="https://use.typekit.net/qxt1mdt.css">
    <meta name="description"
        content="Nejžádanější rozměry sezónních pneumatik máme skladem. Zboží si můžete prohlédnout a také okamžitě zakoupit na pobočce v Havířově.">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="format-detection" content="telephone=no">
    <meta name="robots" content="index,follow">
</head>

<div class="b b-text cf">
    <div class="b-c b-text-c b-s b-s-t60 b-s-b60 b-cs cf">
        <!-- Nu Form -->
        <div style="text-align: center">
            <h1>Katalog Pneu</h1>
            <p>Vyhledejte <strong>nejlevnější pneu</strong> pro svůj vůz dle šířky, profilu, průměru a období.</p>
            <p class="infotips">
                Nejhledanější rozměry:
                <a href="/?sirka=165&profil=70&prumer=R14&obdobi=zimní"><span class="infotip"><strong>165/70
                            R14</strong> zimní</span></a>
                <a href="/?sirka=195&profil=65&prumer=R15&obdobi=zimní"><span class="infotip"><strong>195/65
                            R15</strong> zimní</span></a>
                <a href="/?sirka=205&profil=55&prumer=R16&obdobi=zimní"><span class="infotip"><strong>205/55
                            R16</strong> zimní</span></a>
            </p>
        </div>
        <div class="s009">
            <form>
                <div class="inner-form">
                    <div class="advance-search">
                        <div class="row">
                            <div class="input-field">
                                <div class="input-select">
                                    <select data-trigger="" name="sirka">
                                        <option placeholder="" value="all">Šířka</option>
                                        <?php
                                        foreach ($sirky as $sirka) {
                                            if ($_GET["sirka"] == $sirka['width']) {
                                                echo "<option selected>" . $sirka['width'] . "</option>";
                                            } else {
                                                echo "<option>" . $sirka['width'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="input-field">
                                <div class="input-select">
                                    <select data-trigger="" name="profil">
                                        <option placeholder="" value="all">Profil</option>
                                        <?php
                                        foreach ($profily as $profil) {
                                            if ($_GET["profil"] == $profil['height']) {
                                                echo "<option selected>" . $profil['height'] . "</option>";
                                            } else {
                                                echo "<option>" . $profil['height'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="input-field">
                                <div class="input-select">
                                    <select data-trigger="" name="prumer">
                                        <option placeholder="" value="all">Průměr</option>
                                        <?php
                                        foreach ($prumery as $prumer) {
                                            if ($_GET["prumer"] == $prumer['diameter']) {
                                                echo "<option selected>" . $prumer['diameter'] . "</option>";
                                            } else {
                                                echo "<option>" . $prumer['diameter'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row second">
                            <?php /*             
                                                                      <div class="input-field">                
                                                                          <div class="input-select">                  
                                                                              <select data-trigger="" name="vyrobce">                    
                                                                                  <option placeholder="" value="all">Výrobce</option>
                                                                                  <?php
                                                                                      foreach($vyrobci as $vyrobce) {
                                                                                          if($_GET["vyrobce"] == $vyrobce['producer']) {
                                                                                              echo "<option selected>" . $vyrobce['producer'] . "</option>";
                                                                                          } else {
                                                                                              echo "<option>" . $vyrobce['producer'] . "</option>";
                                                                                          }
                                                                                      }
                                                                                  ?>                    
                                                                              </select>                
                                                                          </div>              
                                                                      </div>  */?>
                            <div class="input-field">
                                <div class="input-select">
                                    <select data-trigger="" name="obdobi">
                                        <option placeholder="" value="all">Období</option>
                                        <?php
                                        foreach ($rocniobdobi as $obdobi) {
                                            if ($_GET["obdobi"] == $obdobi['season']) {
                                                echo "<option selected>" . $obdobi['season'] . "</option>";
                                            } else {
                                                echo "<option>" . $obdobi['season'] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <?php /*            
                                                                      <div class="input-field">                
                                                                          <div class="input-select">
                                                                              <?php if(false): ?>                 
                                                                              <select data-trigger="" name="rychlost">                    
                                                                                  <option placeholder="" value="all">Rychlost</option>                    
                                                                                  <?php
                                                                                      foreach($rychlosti as $rychlost) {
                                                                                          echo "<option>" . $rychlost['speedindex'] . "</option>";
                                                                                      }
                                                                                  ?>    
                                                                              </select>          
                                                                              <?php endif; ?>      
                                                                          </div>              
                                                                      </div> */?>
                        </div>
                        <div class="row third">
                            <div class="input-field">
                                <div class="result-count">
                                </div>
                                <div class="group-btn">
                                    <?php /*<button class="btn-delete" id="delete">Reset
                                                                              </button> */?>
                                    <button class="btn-search">Vyhledat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <?php if (isset($pneux)): ?>
            <div class="searchres" style="text-align:center">
                <?php foreach ($pneux as $pneu) { ?>
                    <div class="tyre">
                        <span class="tyrename"><strong>
                                <?php echo $pneu["name"]; ?>
                            </strong></span>
                        <span class="qty">SKLADEM: <strong>
                                <?php echo $pneu["qty"]; ?>
                            </strong> ks</span>
                        <span class="finalprice">CENA: <strong>
                                <?php echo $pneu["finalprice"]; ?>
                            </strong> Kč</span>
                    </div>
                <?php } ?>
            </div> <!-- DIV searchres END -->
        <?php else: ?>
            <div class="nosearchres" style="color:red">
                * Nebyl zadán dostatečný filtr pro vyhledávání pneu.<br>
                <?php // echo "DOTAZ SQL: " . $query; ?>
            </div>
        <?php endif; ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <script src="../ui/js/choices.js"></script>
        <script>
            const customSelects = document.querySelectorAll("select");
            const deleteBtn = document.getElementById('delete')
            const choices = new Choices('select', {
                searchEnabled: false,
                itemSelectText: '',
                removeItemButton: true,
            });
            deleteBtn.addEventListener("click", function (e) {
                e.preventDefault()
                const deleteAll = document.querySelectorAll('.choices__button')
                for (let i = 0; i < deleteAll.length; i++) {
                    deleteAll[i].click();
                }
            });
        </script>
        <!-- Nu Form -->
    </div>
    <!-- END of b-c b-text-c b-s b-s-t60 b-s-b60 b-cs cf -->
</div>
</section>
</main>
</body>

</html>