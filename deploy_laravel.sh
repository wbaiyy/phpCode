# 相关脚本执行路径
PHP_BIN=/usr/local/bin/php
COMPOSER_BIN=/usr/local/bin/composer
PHPFPM_BIN=/etc/init.d/php-fpm

PATH=${PHP_BIN}:${COMPOSER_BIN}:${PATH}
export PATH

#项目构建目录
declare -A project_mapping
project_mapping=(
    [www-soa]=/data/www/openplatform/www-soa/
    [m-soa]=
    [app-soa]=
)

#生产环境目录(在config:cache后的相关存储目录被替换成生产环境的)
#【注意】务必检测是否替换正确，包括后缀目录符"/"
declare -A prod_app_path
prod_app_path=(
    [www-soa]=/var/www/www.gearbest.com/
    [m-soa]=
    [app-soa]=
)

#进入到项目根目录
enter_project_root() {
    if [ -d "$1" ]; then
        cd $1
        return 0
    else
        help_message "Path {$1} not exist !"
        return 1
    fi
}

#优化脚本
laravel_optimize() {
    echo "Start Laravel Optimize...."
    for cmd in "clear-compiled" "view:clear" "config:cache" "route:cache"
    do
        php artisan $cmd
    done
    echo "[OK]"
    return 0
}

#Replace掉config:cache的路径部分
webpath_replace() {
    build_path=$1
    prod_path=$2

    if [ $(echo $build_path|grep '\/$') ]; then
    	config_file=${build_path}"bootstrap/cache/config.php"
    else
    	config_file=${build_path}"/bootstrap/cache/config.php"
    fi

    if [ -n "$build_path" -a -n "$prod_path" -a -e $config_file ]; then
        echo "Start replace build_path to  prod_path in {$config_file}..."
        sed -i "s#$build_path#$prod_path#g" $config_file
        #查看替换明细
        grep $prod_path $config_file
        echo "[OK]"
    fi
}

#Composer安装依赖包
composer_install() {
    echo "Start Composer install..."
    composer install --no-dev -o
    echo "[OK]"
    return 0
}


#重启phpfpm服务,避免Opcache的影响
restart_opcache() {
    echo "Restart PHP Opcache..."
    $PHPFPM_BIN restart    
    echo "[OK]"
    return 0
}

#打印输出字符
print_separate_line() {
    printf "%60s\n"|sed 's/\s/-/g'
}

#帮助信息
help_message() {
    print_separate_line
    echo -e "Error: ${1:-Bad command}; \nUsage: $0 optimize <site_code> (eg. www-soa|app-soa|m-soa...)"
    print_separate_line
    exit 1;
}

case $1 in 
    optimize)
        if [ -z "$2" ]; then
            help_message "site code cannot be empty !!"
        else
            build_path=${project_mapping[$2]}
            prod_path=${prod_app_path[$2]}

            #进入构建目录
            enter_project_root $build_path

            #Composer安装
            print_separate_line
            composer_install

            #L框架优化
            print_separate_line
            laravel_optimize

            #目标地址替换
            webpath_replace $build_path $prod_path

            #Opcache清理
            print_separate_line
            restart_opcache

            print_separate_line
        fi
        ;;
    --help|-h)
        help_message
        ;;
    *)
        help_message
        ;;
esac
